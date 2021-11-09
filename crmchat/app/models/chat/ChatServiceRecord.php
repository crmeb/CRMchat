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
 * 聊天记录
 * Class ChatServiceRecord
 * @package app\models\chat
 */
class ChatServiceRecord extends BaseModel
{
    use ModelTrait;

    /**
     * 表名
     * @var string
     */
    protected $name = 'chat_service_record';

    /**
     * 主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 更新时间
     * @var bool | string | int
     */
    protected $updateTime = false;

    /**
     * 客服用户
     * @return \think\model\relation\HasOne
     */
    public function service()
    {
        return $this->hasOne(ChatService::class, 'uid', 'to_uid')->field(['nickname', 'uid', 'avatar'])->bind([
            'kefu_nickname' => 'nickname',
            'kefu_avatar' => 'avatar',
        ]);
    }

    /**
     * 客服用户关联
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(ChatUser::class, 'id', 'to_user_id')->field(['id', 'nickname', 'remark_nickname', 'version']);
    }

    /**
     * 发送者id搜索器
     * @param Model $query
     * @param $value
     */
    public function searchUserIdAttr($query, $value)
    {
        if ($value) {
            $query->where('user_id', $value);
        }
    }

    /**
     * 送达人uid搜索器
     * @param Model $query
     * @param $value
     */
    public function searchToUserIdAttr($query, $value)
    {
        $query->where('to_user_id', $value);
    }

    /**
     * 用户昵称搜索器
     * @param Model $query
     * @param $value
     */
    public function searchTitleAttr($query, $value)
    {
        if ($value) {
            $query->where(function ($query) use ($value) {
                $query->whereLike('nickname|user_id', '%' . $value . '%');
            });
        }
    }

    /**
     * 是否游客
     * @param Model $query
     * @param $value
     */
    public function searchIsTouristAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('is_tourist', $value);
        }
    }

    /**
     *
     * @param $value
     * @param $data
     * @return string
     */
    public function getNickNameAttr($value, $data)
    {
        if (isset($this->user->version) && $this->user->version) {
            return '[.' . $this->user->version . '.]' . $value;
        } else {
            return $value;
        }
    }
}
