<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\controller\admin\chat;


use app\controller\admin\AuthController;
use app\services\chat\ChatAutoReplyServices;

/**
 * Class AutoReply
 * @package app\controller\admin\chat
 */
class AutoReply extends AuthController
{

    /**
     * AutoReply constructor.
     * @param ChatAutoReplyServices $services
     */
    public function __construct(ChatAutoReplyServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    public function index()
    {
        [$userId, $appId] = $this->request->getMore([
            ['user_id', 0],
            ['appid', ''],
        ], true);
        return $this->success($this->services->getList($appId, (int)$userId));
    }

    /**
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create($id)
    {
        $userId = $this->request->get('user_id', 0);
        $appId = $this->request->get('appid', '');
        return $this->success($this->services->getForm((int)$id, (int)$userId, $appId));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function save($id)
    {
        $data = $this->request->postMore([
            ['keyword', ''],
            ['content', ''],
            ['user_id', ''],
            ['appid', ''],
            ['sort', 0],
        ]);

        if (!$data['keyword']) {
            return $this->fail('请输入关键字');
        }
        if (!$data['content']) {
            return $this->fail('请输入回复内容');
        }

        if ($id) {
            $this->services->update($id, $data);
        } else {
            $data['add_time'] = time();
            $this->services->save($data);
        }
        return $this->success($id ? '修改成功' : '保存成功');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        $this->services->delete($id);

        return $this->success('删除成功');
    }
}
