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

use app\services\other\CacheServices;
use app\controller\admin\AuthController;
use app\services\system\config\SystemGroupDataServices;
use app\services\system\config\SystemGroupServices;

/**
 * 数据管理
 * Class GroupData
 * @package app\controller\admin\system
 */
class GroupData extends AuthController
{
    /**
     * 构造方法
     * SystemGroupData constructor.
     * @param SystemGroupDataServices $services
     */
    public function __construct(SystemGroupDataServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 获取数据列表头
     * @return mixed
     */
    public function header(SystemGroupServices $services)
    {
        $gid = $this->request->param('gid/d');
        if (!$gid) return $this->fail('参数错误');
        return $this->success($services->getGroupDataTabHeader($gid));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['gid', 0],
            ['status', ''],
        ]);
        return $this->success($this->services->getGroupDataList($where));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $gid = $this->request->param('gid/d');
        if ($this->services->isGroupGidSave($gid, 4, 'index_categy_images')) {
            return $this->fail('不能大于四个！');
        }
        if ($this->services->isGroupGidSave($gid, 7, 'sign_day_num')) {
            return $this->fail('签到天数配置不能大于7天');
        }
        return $this->success($this->services->createForm($gid));
    }

    /**
     * 保存新建的资源
     *
     * @return \think\Response
     */
    public function save(SystemGroupServices $services)
    {
        $params = request()->post();
        $gid = (int)$params['gid'];
        $group = $services->getOne(['id' => $gid], 'id,config_name,fields');
        if ($group && $group['config_name'] == 'order_details_images') {
            $groupDatas = $this->services->getColumn(['gid' => $gid], 'value', 'id');
            foreach ($groupDatas as $groupData) {
                $groupData = json_decode($groupData, true);
                if (isset($groupData['order_status']['value']) && $groupData['order_status']['value'] == $params['order_status']) {
                    return $this->fail('已存在请不要重复添加');
                }
            }
        }

        $fields = json_decode($group['fields'], true) ?? [];
        $value = [];
        foreach ($params as $key => $param) {
            foreach ($fields as $index => $field) {
                if ($key == $field["title"]) {
                    if ($param == "")
                        return $this->fail($field["name"] . "不能为空！");
                    else {
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }
        $data = [
            "gid" => $params['gid'],
            "add_time" => time(),
            "value" => json_encode($value),
            "sort" => $params["sort"] ?: 0,
            "status" => $params["status"]
        ];
        $this->services->save($data);
        \crmeb\services\CacheService::clear();
        return $this->success('添加数据成功!');
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $gid = $this->request->param('gid/d');
        if (!$gid) {
            return $this->fail('缺少参数');
        }
        return $this->success($this->services->updateForm((int)$gid, (int)$id));
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(SystemGroupServices $services, $id)
    {
        $groupData = $this->services->get($id);
        $fields = $services->getValueFields((int)$groupData["gid"]);
        $params = request()->post();
        foreach ($params as $key => $param) {
            foreach ($fields as $index => $field) {
                if ($key == $field["title"]) {
                    if ($param == '')
                        return $this->fail($field["name"] . "不能为空！");
                    else {
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }
        $data = [
            "value" => json_encode($value),
            "sort" => $params["sort"],
            "status" => $params["status"]
        ];
        $this->services->update($id, $data);
        \crmeb\services\CacheService::clear();
        return $this->success('修改成功!');
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$this->services->delete($id))
            return $this->fail('删除失败,请稍候再试!');
        else {
            \crmeb\services\CacheService::clear();
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
        if ($status == '' || $id == 0) return $this->fail('参数错误');
        $this->services->update($id, ['status' => $status]);
        \crmeb\services\CacheService::clear();
        return $this->success($status == 0 ? '隐藏成功' : '显示成功');
    }

    /**
     * 获取客服页面广告内容
     * @return mixed
     */
    public function getKfAdv()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $content = $cache->getDbCache('kf_adv', '');
        return $this->success(compact('content'));
    }

    /**
     * 设置客服页面广告内容
     * @return mixed
     */
    public function setKfAdv()
    {
        $content = $this->request->post('content');
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $cache->setDbCache('kf_adv', $content);
        return $this->success('设置成功');
    }

    /**
     * 获取用户协议内容
     * @return mixed
     */
    public function getUserAgreement()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $content = $cache->getDbCache('user_agreement', '');
        return $this->success(compact('content'));
    }

    /**
     * 设置用户协议内容
     * @return mixed
     */
    public function setUserAgreement()
    {
        $content = $this->request->post('content');
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $cache->setDbCache('user_agreement', $content);
        return $this->success('设置成功');
    }
}
