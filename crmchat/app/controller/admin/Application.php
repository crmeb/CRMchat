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

namespace app\controller\admin;


use app\services\ApplicationServices;
use crmeb\utils\Encrypter;
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 应用管理
 * Class Application
 * @package app\controller\admin
 */
class Application extends AuthController
{

    /**
     * Application constructor.
     * @param ApplicationServices $services
     */
    public function __construct(ApplicationServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 获取应用列表数据
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['name', '', 'name_like'],
        ]);

        return $this->success($this->services->getList($where));
    }

    /**
     * 获取创建数据
     * @return mixed
     * @throws FormBuilderException
     */
    public function create()
    {
        return $this->success($this->services->getCreateForm());
    }

    /**
     * 重置token
     * @param $id
     * @return mixed
     */
    public function reset($id)
    {
        $appInfo = $this->services->get($id);
        if (!$appInfo) {
            return $this->fail('应用不存在');
        }
        $rand               = rand(1000, 9999);
        $time               = time();
        $data['rand']       = $rand;
        $data['timestamp']  = $time;
        $data['app_secret'] = md5($appInfo->appid . $time . $rand);
        /** @var Encrypter $encrypter */
        $encrypter = app()->make(Encrypter::class);

        $data['token']     = $encrypter->encrypt(json_encode([
            'appid'      => $appInfo->appid,
            'app_secret' => $data['app_secret'],
            'rand'       => $rand,
            'timestamp'  => $time,
        ]));
        $data['token_md5'] = md5($data['token']);
        $this->services->update($id, $data);

        return $this->success($data);
    }

    /**
     * 保存数据
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['icon', ''],
            ['name', ''],
            ['introduce', ''],
        ]);

        $rand               = rand(1000, 9999);
        $time               = time();
        $data['appid']      = date('Y') . $time . $rand;
        $data['rand']       = $rand;
        $data['timestamp']  = $time;
        $data['app_secret'] = md5($data['appid'] . $time . $rand);
        /** @var Encrypter $encrypter */
        $encrypter = app()->make(Encrypter::class);

        $data['token']     = $encrypter->encrypt(json_encode([
            'appid'      => $data['appid'],
            'app_secret' => $data['app_secret'],
            'rand'       => $rand,
            'timestamp'  => $time,
        ]));
        $data['token_md5'] = md5($data['token']);
        $data['is_delete'] = 0;
        if (!$data['icon']) {
            return $this->fail('请选择应用图标');
        }
        if (!$data['name']) {
            return $this->fail('请填写应用名称');
        }
        if ($this->services->count(['name' => $data['name']])) {
            return $this->fail('应用名称已存在');
        }

        if ($this->services->save($data)) {
            return $this->success('保存成功');
        } else {
            return $this->fail('保存失败');
        }
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

        return $this->success($this->services->getUpdateForm((int)$id));
    }

    /**
     * 更新
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['icon', ''],
            ['name', ''],
            ['introduce', ''],
        ]);

        if (!$data['icon']) {
            return $this->fail('请选择应用图标');
        }
        if (!$data['name']) {
            return $this->fail('请填写应用名称');
        }
        $this->services->update($id, $data);
        return $this->success('保存成功');
    }

    /**
     * 删除
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        $this->services->update($id, ['is_delete' => 1]);
        return $this->success('删除成功');
    }


}
