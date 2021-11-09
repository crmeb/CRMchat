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


use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatUserServices;
use app\services\system\SystemMenusServices;

/**
 * 首页
 * Class Index
 * @package app\controller\admin
 */
class Index extends AuthController
{

    /**
     * Index constructor.
     * @param ChatServiceRecordServices $services
     */
    public function __construct(ChatServiceRecordServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 获取log
     * @return mixed
     */
    public function logo()
    {
        return $this->success([
            'logo'        => link_url(sys_config('site_logo')),
            'logo_square' => link_url(sys_config('site_logo_square')),
            'site_name'   => sys_config('site_name')
        ]);
    }

    /**
     * @return mixed
     */
    public function jnotice()
    {
        return $this->success([]);
    }

    /**
     * 获取菜单
     * @return mixed
     */
    public function getMenusList()
    {
        /** @var SystemMenusServices $menusServices */
        $menusServices = app()->make(SystemMenusServices::class);
        $list          = $menusServices->getSearchList();
        $counts        = $menusServices->getColumn([
            ['is_show', '=', 1],
            ['auth_type', '=', 1],
            ['is_del', '=', 0],
            ['is_show_path', '=', 0],
        ], 'pid');
        $data          = [];
        foreach ($list as $key => $item) {
            $pid               = $item->getData('pid');
            $data[$key]        = json_decode($item, true);
            $data[$key]['pid'] = $pid;
            if (in_array($item->id, $counts)) {
                $data[$key]['type'] = 1;
            } else {
                $data[$key]['type'] = 0;
            }
        }
        return app('json')->success(sort_list_tier($data));
    }

    /**
     * 客户统计
     * @return mixed
     */
    public function sum(ChatUserServices $services)
    {
        return $this->success($services->getKefuSum());
    }

    /**
     * 客户首页统计
     * @return mixed
     */
    public function index(ChatUserServices $services)
    {
        $type  = $this->request->get('type', 0);
        $year  = $this->request->get('year', date('Y'));
        $month = $this->request->get('month', date('m'));
        if ($month <= 0 || $month > 12) {
            return $this->fail('月份错误');
        }
        if ($year[0] > 2) {
            return $this->fail('年份错误');
        }
        if (strlen($year) > 4) {
            return $this->fail('年份错误');
        }
        return $this->success($services->getKefuStatistics(0, (int)$type, (int)$year, (int)$month));
    }

}
