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
 * 对话记录
 * Class ChatServiceDialogueRecord
 * @package app\models\chat
 */
class ChatServiceDialogueRecord extends BaseModel
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
    protected $name = 'chat_service_dialogue_record';

    public function getAddTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * @param $value
     * @return mixed|object
     */
    public function getOtherAttr($value)
    {
        return $value ? json_decode($value, true) : (object)[];
    }

    /**
     * 一对一关联
     * @return mixed
     */
    public function service()
    {
        return $this->hasOne(ChatService::class, 'uid', 'uid')->field(['uid', 'nickname', 'avatar'])->bind([
            'nickname' => 'nickname',
            'avatar' => 'avatar'
        ]);
    }

    /**
     * 客服用户关联
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(ChatUser::class, 'id', 'to_user_id')->field(['id', 'nickname', 'remark_nickname']);
    }

    /**
     * @return \think\model\relation\HasOne
     */
    public function userThis()
    {
        return $this->hasOne(ChatUser::class, 'id', 'user_id')
            ->field(['id', 'nickname', 'avatar']);
    }

    /**
     * @return \think\model\relation\HasOne
     */
    public function userTo()
    {
        return $this->hasOne(ChatUser::class, 'id', 'to_user_id')
            ->field(['id', 'nickname', 'avatar']);
    }

    /**
     * uid搜索器
     * @param Model $query
     * @param $value
     */
    public function searchUserIdAttr($query, $value)
    {
        $query->where('user_id|to_user_id', $value);
    }

    /**
     * 聊天记录搜索器
     * @param Model $query
     * @param $value
     */
    public function searchChatAttr($query, $value)
    {
        $query->whereIn('user_id', $value)->whereIn('to_user_id', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchIsTouristAttr($query, $value)
    {
        $query->where('is_tourist', $value);
    }
}

