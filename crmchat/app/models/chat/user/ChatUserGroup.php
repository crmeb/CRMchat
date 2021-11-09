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

namespace app\models\chat\user;


use crmeb\basic\BaseModel;

/**
 * Class ChatUserGroup
 * @package app\models\chat\user
 */
class ChatUserGroup extends BaseModel
{

    /**
     * @var string
     */
    protected $name = 'chat_user_group';

    /**
     * @var string
     */
    protected $key = 'id';
}
