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

namespace app\controller\kefu;


use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatUserServices;

/**
 * Class Statistics
 * @package app\controller\kefu
 */
class Statistics extends AuthController
{

    /**
     * Statistics constructor.
     * @param ChatServiceRecordServices $services
     */
    public function __construct(ChatServiceRecordServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 客户统计
     * @return mixed
     */
    public function sum(ChatUserServices $services)
    {
        return $this->success($services->getKefuSum($this->kefuInfo['appid']));
    }

    /**
     * 客户首页统计
     * @return mixed
     */
    public function index(ChatUserServices $services)
    {
        $time = $this->request->get('time', '');
        return $this->success($services->getKefuMobileStatistics($time));
    }
}
