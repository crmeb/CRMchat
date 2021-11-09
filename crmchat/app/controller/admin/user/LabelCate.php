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
use app\services\chat\user\ChatUserLabelCateServices;
use app\services\chat\user\ChatUserLabelServices;

class LabelCate extends AuthController
{
    /**
     * LabelCate constructor.
     * @param ChatUserLabelCateServices $services
     */
    public function __construct(ChatUserLabelCateServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        return $this->success($this->services->getCateList(['type' => 0]));
    }

    /**
     * 获取创建表单
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return $this->success($this->services->getCreateForm());
    }

    /**
     * 创建新数据
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['name', ''],
        ]);
        if (!$data['name']) {
            return $this->fail('请输入分类名称');
        }
        if ($this->services->count(['name' => $data['name'], 'type' => 0])) {
            return $this->fail('分类名称相同');
        }
        $data['type']     = 0;
        $data['add_time'] = time();
        $this->services->save($data);
        return $this->success('添加成功');
    }

    /**
     * 获取修改表单
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        return $this->success($this->services->getEditForm((int)$id));
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['name', ''],
        ]);
        if (!$data['name']) {
            return $this->fail('请输入分类名称');
        }

        $data['type'] = 0;

        $this->services->update($id, $data);
        return $this->success('修改成功');
    }

    /**
     * 删除标签分类
     * @param ChatUserLabelServices $services
     * @param $id
     * @return mixed
     */
    public function delete(ChatUserLabelServices $services, $id)
    {

        if (!$id) {
            return $this->fail('缺少参数');
        }

        if ($services->count(['cate_id' => $id])) {
            return $this->fail('请先删除分类下的标签');
        }
        $this->services->delete($id);
        return $this->success('删除成功');
    }
}
