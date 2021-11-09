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
use app\services\system\admin\SystemAdminServices;
use app\services\system\log\SystemLogServices;

/**
 * Class Log
 * @package app\controller\admin\system
 */
class Log extends AuthController
{

    /**
     * Log constructor.
     * @param SystemLogServices $services
     */
    public function __construct(SystemLogServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 显示操作记录
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['pages', ''],
            ['path', ''],
            ['ip', ''],
            ['admin_id', ''],
            ['data', '', '', 'time'],
        ]);
        return $this->success($this->services->getLogList($where, (int)$this->adminInfo['level']));
    }

    /**
     * 新增接口
     * @param SystemAdminServices $services
     * @return mixed
     */
    public function search_admin(SystemAdminServices $services)
    {
        $info = $services->getOrdAdmin('id,real_name', $this->adminInfo['level']);
        return $this->success(compact('info'));
    }
}
