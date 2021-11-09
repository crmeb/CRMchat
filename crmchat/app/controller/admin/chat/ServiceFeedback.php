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
use app\services\chat\ChatServiceFeedbackServices;
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 反馈
 * Class ServiceFeedback
 * @package app\controller\admin\chat
 */
class ServiceFeedback extends AuthController
{

    /**
     * ServiceFeedback constructor.
     * @param ChatServiceFeedbackServices $services
     */
    public function __construct(ChatServiceFeedbackServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 获取留言列表
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['title', ''],
            ['time', '']
        ]);

        return $this->success($this->services->getFeedbackList($where));
    }

    /**
     * 获取修改表单
     * @param $id
     * @return mixed
     * @throws FormBuilderException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function edit($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        return $this->success($this->services->editForm((int)$id));
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['make', ''],
            ['status', 0],
        ]);
        if (!$id || !($feedInfo = $this->services->get($id))) {
            return $this->fail('反馈内容不存在');
        }
        $feedInfo->make = $data['make'];
        if ($data['status']) {
            $feedInfo->status = $data['status'];
        }
        $feedInfo->save();
        return $this->success('修改成功');
    }

    /**
     * 删除反馈
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        if ($this->services->delete($id)) {
            return $this->success('删除成功');
        } else {
            return $this->fail('删除失败');
        }
    }
}
