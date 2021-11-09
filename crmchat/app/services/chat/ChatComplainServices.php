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

namespace app\services\chat;


use app\dao\chat\ChatComplainDao;
use crmeb\basic\BaseServices;

/**
 * Class ChatComplainServices
 * @package app\services\chat
 */
class ChatComplainServices extends BaseServices
{

    /**
     * ChatComplainServices constructor.
     * @param ChatComplainDao $dao
     */
    public function __construct(ChatComplainDao $dao)
    {
        $this->dao = $dao;
    }

}
