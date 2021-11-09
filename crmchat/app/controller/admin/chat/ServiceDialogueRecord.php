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
use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceServices;

/**
 * Class ServiceDialogueRecord
 * @package app\controller\admin\chat
 */
class ServiceDialogueRecord extends AuthController
{

    /**
     * ServiceDialogueRecord constructor.
     * @param ChatServiceDialogueRecordServices $services
     */
    public function __construct(ChatServiceDialogueRecordServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    public function kefu(ChatServiceServices $services)
    {
        return $this->success($services->getColumn(['status' => 1], 'appid,id,nickname'));
    }

    public function index()
    {
        $where = $this->request->getMore([
            ['kefu_id', ''],
            ['msn', ''],
            ['appid', '']
        ]);

        return $this->success($this->services->getDialogueRecord($where));
    }
}
