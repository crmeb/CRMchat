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

namespace app\models\chat;


use crmeb\basic\BaseModel;

/**
 * Class ChatAutoReply
 * @package app\models\chat
 */
class ChatAutoReply extends BaseModel
{

    /**
     * @var string
     */
    protected $name = 'chat_auto_reply';

    /**
     * @var string
     */
    protected $key = 'id';

    /**
     * @param $query
     * @param $value
     */
    public function searchAppidAttr($query, $value)
    {
        $query->where('appid', $value);
    }

    /**
     * @param $query
     * @param $value
     */
    public function searchUserIdAttr($query, $value)
    {
        $query->where('user_id', $value);
    }
}
