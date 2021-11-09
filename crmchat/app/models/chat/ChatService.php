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
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Class ChatService
 * @package app\models\chat
 */
class ChatService extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'chat_service';

    /**
     * @var bool
     */
    protected $updateTime = false;

    protected function getAddTimeAttr($value)
    {
        if ($value) return date('Y-m-d H:i:s', $value);
        return $value;
    }


    /**
     * uid搜索器
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        $query->where('uid', $value);
    }

    /**
     * status搜索器
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * status搜索器
     * @param Model $query
     * @param $value
     */
    public function searchAccountStatusAttr($query, $value)
    {
        $query->where('account_status', $value);
    }

    /**
     * account搜索器
     * @param Model $query
     * @param $value
     */
    public function searchAccountAttr($query, $value)
    {
        $query->where('account', $value);
    }

    /**
     * phone搜索器
     * @param Model $query
     * @param $value
     */
    public function searchPhoneAttr($query, $value)
    {
        $query->where('phone', $value);
    }

    /**
     * customer
     * @param Model $query
     * @param $value
     */
    public function searchCustomerAttr($query, $value)
    {
        $query->where('customer', $value);
    }

    /**
     * 用户昵称搜索器
     * @param Model $query
     * @param $value
     */
    public function searchNicknameAttr($query, $value)
    {
        if ($value) $query->whereLike('nickname', '%' . $value . '%');
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchOnlineAttr($query, $value)
    {
        $query->where('online', $value);
    }

    /**
     * 用户uid搜索器
     * @param Model $query
     * @param $value
     */
    public function searchNoUidAttr($query, $value)
    {
        if ($value) $query->whereNotIn('uid', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchUserIdAttr($query, $value)
    {
        $query->where('user_id', $value);
    }
}
