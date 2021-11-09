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

namespace app\controller\admin\user;


use app\controller\admin\AuthController;
use app\services\chat\user\ChatUserLabelAssistServices;
use app\services\chat\user\ChatUserLabelServices;

/**
 * Class Label
 * @package app\controller\admin\user
 */
class Label extends AuthController
{

    /**
     * Label constructor.
     * @param ChatUserLabelServices $services
     */
    public function __construct(ChatUserLabelServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 获取标签
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where            = $this->request->getMore([
            ['cate_id', ''],
        ]);
        $where['user_id'] = 0;
        return $this->success($this->services->getLabeList($where));
    }

    /**
     * 创建表单
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return $this->success($this->services->getFormCreate());
    }

    /**
     * 保存标签
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['cate_id', 0],
            ['label', ''],
        ]);
        if (!$data['label']) {
            return $this->fail('标签名称必须填写');
        }
        if (!$data['cate_id']) {
            return $this->fail('请选择标签分类');
        }

        $this->services->save($data);

        return $this->success('保存成功');
    }

    /**
     * 获取修改表单
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        return $this->success($this->services->getFormEdit((int)$id));
    }

    /**
     * 更新
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['cate_id', 0],
            ['label', ''],
        ]);
        if (!$data['label']) {
            return $this->fail('标签名称必须填写');
        }
        if (!$data['cate_id']) {
            return $this->fail('请选择标签分类');
        }

        $this->services->update($id, $data);

        return $this->success('修改成功');
    }

    /**
     * 删除
     * @param $id
     * @return mixed
     */
    public function delete(ChatUserLabelAssistServices $services, $id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        if ($services->count(['label_id' => $id])) {
            return $this->fail('请先取消关联此标签的用户');
        }
        $this->services->delete($id);

        return $this->success('删除成功');
    }

}
