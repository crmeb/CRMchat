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
namespace app\controller\admin\system;

use app\controller\admin\AuthController;
use app\Request;
use app\services\system\config\SystemConfigServices;
use app\services\system\config\SystemConfigTabServices;
use app\validate\system\SystemConfigValidata;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

/**
 * 系统配置
 * Class SystemConfig
 * @package app\controller\admin\system
 */
class Config extends AuthController
{

    /**
     * SystemConfig constructor.
     * @param SystemConfigServices $services
     */
    public function __construct(SystemConfigServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 显示资源列表
     *
     * @return Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['tab_id', 0],
            ['status', -1]
        ]);
        if (!$where['tab_id']) {
            return $this->fail('参数错误');
        }
        if ($where['status'] == -1) {
            unset($where['status']);
        }
        return $this->success($this->services->getConfigList($where));
    }

    /**
     * 显示创建资源表单页.
     * @param $type
     * @return Response
     */
    public function create()
    {
        [$type, $tabId] = $this->request->getMore([
            [['type', 'd'], ''],
            [['tab_id', 'd'], 1]
        ], true);
        return $this->success($this->services->createFormRule($type, $tabId));
    }

    /**
     * 保存新建的资源
     *
     * @return Response
     */
    public function save()
    {
        $data = $this->request->postMore([
            'menu_name',
            'type',
            ['input_type', ''],
            'config_tab_id',
            ['parameter', ''],
            ['upload_type', ''],
            ['required', ''],
            ['width', 0],
            ['high', 0],
            ['value', ''],
            ['info', ''],
            ['desc', ''],
            ['sort', 0],
            ['status', ''],
        ]);
        if (!$data['info']) return $this->fail('请输入配置名称');
        if (!$data['menu_name']) return $this->fail('请输入字段名称');
        if (!$data['desc']) return $this->fail('请输入配置简介');
        if ($data['sort'] < 0) {
            $data['sort'] = 0;
        }
        if ($data['type'] == 'text') {
            if (!$data['width']) return $this->fail('请输入文本框的宽度');
            if ($data['width'] <= 0) return $this->fail('请输入正确的文本框的宽度');
        }
        if ($data['type'] == 'textarea') {
            if (!$data['width']) return $this->fail('请输入多行文本框的宽度');
            if (!$data['high']) return $this->fail('请输入多行文本框的高度');
            if ($data['width'] < 0) return $this->fail('请输入正确的多行文本框的宽度');
            if ($data['high'] < 0) return $this->fail('请输入正确的多行文本框的宽度');
        }
        if ($data['type'] == 'radio' || $data['type'] == 'checkbox') {
            if (!$data['parameter']) return $this->fail('请输入配置参数');
            $this->services->valiDateRadioAndCheckbox($data);
        }
        $data['value'] = json_encode($data['value']);
        $config = $this->services->getOne(['menu_name' => $data['menu_name']]);
        if ($config) {
            $this->services->update($config['id'], $data, 'id');
        } else {
            $this->services->save($data);
        }
        \crmeb\services\SystemConfigService::clear();
        return $this->success('添加配置成功!');
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return Response
     */
    public function read($id)
    {
        if (!$id) {
            return $this->fail('参数错误，请重新打开');
        }
        $info = $this->services->getReadList((int)$id);
        return $this->success(compact('info'));
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return $this->success($this->services->editConfigForm((int)$id));
    }

    /**
     * 保存更新的资源
     *
     * @param int $id
     * @return Response
     */
    public function update($id)
    {
        $type = request()->post('type');
        if ($type == 'text' || $type == 'textarea' || $type == 'radio' || ($type == 'upload' && (request()->post('upload_type') == 1 || request()->post('upload_type') == 3))) {
            $value = request()->post('value');
        } else {
            $value = request()->post('value/a');
        }
        if (!$value) $value = request()->post(request()->post('menu_name'));
        $data = $this->request->postMore(['status', 'info', 'desc', 'sort', 'config_tab_id', 'required', 'parameter', ['value', $value], 'upload_type', 'input_type']);
        if (!$this->services->get($id)) {
            return $this->fail('编辑的记录不存在!');
        }
        $data['value'] = json_encode($data['value']);
        $this->services->update($id, $data);
        \crmeb\services\SystemConfigService::clear();
        return $this->success('修改成功!');
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return Response
     */
    public function delete($id)
    {
        if (!$this->services->delete($id))
            return $this->fail('删除失败,请稍候再试!');
        else {
            \crmeb\services\SystemConfigService::clear();
            return $this->success('删除成功!');
        }
    }

    /**
     * 修改状态
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if ($status == '' || $id == 0) {
            return $this->fail('参数错误');
        }
        $this->services->update($id, ['status' => $status]);
        \crmeb\services\SystemConfigService::clear();
        return $this->success($status == 0 ? '隐藏成功' : '显示成功');
    }

    /**
     * 基础配置
     * */
    public function edit_basics(Request $request)
    {
        $tabId = $this->request->param('tab_id', 1);
        if (!$tabId) {
            return $this->fail('参数错误');
        }
        $url = $request->baseUrl();
        return $this->success($this->services->getConfigForm($url, $tabId));
    }

    /**
     * 保存数据    true
     * */
    public function save_basics(Request $request)
    {
        $post = $this->request->post();
        foreach ($post as $k => $v) {
            if (is_array($v)) {
                $res = $this->services->getUploadTypeList($k);
                foreach ($res as $kk => $vv) {
                    if ($kk == 'upload') {
                        if ($vv == 1 || $vv == 3) {
                            $post[$k] = $v[0];
                        }
                    }
                }
            }
        }
        $this->validate($post, SystemConfigValidata::class);
        if (isset($post['upload_type'])) {
            $this->services->checkThumbParam($post);
        }
        if (isset($post['store_brokerage_binding_status'])) {
            $this->services->checkBrokerageBinding($post);
        }
        if (isset($post['store_brokerage_ratio']) && isset($post['store_brokerage_two'])) {
            $num = $post['store_brokerage_ratio'] + $post['store_brokerage_two'];
            if ($num > 100) {
                return $this->fail('一二级返佣比例不能大于100%');
            }
        }
        if (isset($post['spread_banner'])) {
            $num = count($post['spread_banner']);
            if ($num > 5) {
                return $this->fail('分销海报不能多于5张');
            }
        }
        if (isset($post['user_extract_min_price'])) {
            if (!preg_match('/[0-9]$/', $post['user_extract_min_price'])) {
                return $this->fail('提现最低金额只能为数字!');
            }
        }
        foreach ($post as $k => $v) {
            $config_one = $this->services->getOne(['menu_name' => $k]);
            if ($config_one) {
                $config_one['value'] = $v;
                $this->services->valiDateValue($config_one);
                $this->services->update($k, ['value' => json_encode($v)], 'menu_name');
            }
        }
        if (isset($post['wss_open'])) {
            $this->services->saveSslFilePath((int)$post['wss_open'], $post['wss_local_pk'] ?? '', $post['wss_local_cert'] ?? '');
        }
        \crmeb\services\SystemConfigService::clear();
        return $this->success('修改成功');

    }

    /**
     * 获取系统设置头部分类
     * @param SystemConfigTabServices $services
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function header_basics(SystemConfigTabServices $services)
    {
        [$type, $pid] = $this->request->getMore([
            [['type', 'd'], 0],
            [['pid', 'd'], 0]
        ], true);
        if ($type == 3) {//其它分类
            $config_tab = [];
        } else {
            $config_tab = $services->getConfigTab($pid);
        }
        return $this->success(compact('config_tab'));
    }

}
