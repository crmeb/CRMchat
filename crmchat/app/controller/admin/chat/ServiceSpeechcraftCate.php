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
use app\Request;
use app\services\chat\ChatServiceSpeechcraftCateServices;
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

/**
 * 话术分类
 * Class ServiceSpeechcraftCate
 * @package app\controller\admin\chat
 */
class ServiceSpeechcraftCate extends AuthController
{

    /**
     * ServiceSpeechcraftCate constructor.
     * @param ChatServiceSpeechcraftCateServices $services
     */
    public function __construct(ChatServiceSpeechcraftCateServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 获取列表
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['name', '']
        ]);
        $where['owner_id'] = 0;
        $where['type'] = 1;
        return $this->success($this->services->getCateList($where));
    }

    /**
     * 获取创建表单
     * @return mixed
     * @throws FormBuilderException
     */
    public function create()
    {
        return $this->success($this->services->createForm());
    }

    /**
     * 保存数据
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['sort', 0],
        ]);

        if (!$data['name']) {
            return $this->fail('请输入分类名称');
        }

        if ($this->services->count(['name' => $data['name']])) {
            return $this->fail('分类已存在');
        }

        $data['add_time'] = time();
        $data['type'] = 1;

        $this->services->save($data);
        return $this->success('添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return Response
     */
    public function read($id)
    {
        $info = $this->services->get($id);
        if (!$info) {
            return $this->fail('获取失败');
        }
        return $this->success($info->toArray());
    }

    /**
     * 获取修改表单
     * @param $id
     * @return mixed
     * @throws FormBuilderException
     */
    public function edit($id)
    {
        return $this->success($this->services->editForm((int)$id));
    }

    /**
     * 修改保存
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $data = $request->postMore([
            ['name', ''],
            [['sort', 'd'], 0],
        ]);
        if (!$data['name']) {
            return $this->fail('请输入分类名称');
        }

        $cateInfo = $this->services->get($id);
        if (!$cateInfo) {
            return $this->fail('修改的分类不存在');
        }
        $cateInfo->name = $data['name'];
        $cateInfo->sort = $data['sort'];
        $cateInfo->save();
        return $this->success('修改成功');
    }

    /**
     * 删除
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $cateInfo = $this->services->get($id);
        if (!$cateInfo) {
            return $this->fail('删除的分类不存在');
        }
        $cateInfo->delete();
        return $this->success('删除成功');
    }
}
