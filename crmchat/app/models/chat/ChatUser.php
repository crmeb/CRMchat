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


use app\models\chat\user\ChatUserGroup;
use app\models\chat\user\ChatUserLabel;
use app\models\chat\user\ChatUserLabelAssist;
use crmeb\basic\BaseModel;
use crmeb\traits\TimeModelTrait;
use think\Model;

/**
 * 客户
 * Class ChatUser
 * @package app\models\chat
 */
class ChatUser extends BaseModel
{

    use TimeModelTrait;

    /**
     * 表名
     * @var string
     */
    protected $name = 'chat_user';

    /**
     * 主键id
     * @var string
     */
    protected $key = 'id';

    /**
     * @return \think\model\relation\HasManyThrough
     */
    public function label()
    {
        return $this->hasManyThrough(ChatUserLabel::class, ChatUserLabelAssist::class, 'user_id', 'id', 'id', 'label_id');
    }

    /**
     * @return \think\model\relation\HasOne
     */
    public function groupOne()
    {
        return $this->hasOne(ChatUserGroup::class, 'id', 'group_id');
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchNicknameLikeAttr($query, $value)
    {
        if ($value) {
            $query->where(function ($query) use ($value) {
                $query->where('nickname|phone', 'like', '%' . $value . '%');
            });
        }
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchGroupIdAttr($query, $value)
    {
        if ($value) {
            $query->where('group_id', $value);
        }
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchAppidAttr($query, $value)
    {
        if ($value) {
            $query->where('appid', $value);
        }
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchUserTypeAttr($query, $value)
    {
        //0 = pc,1=微信，2=小程序，3=H5
        if ($value != '') {
            switch ($value) {
                case 'routine':
                    $query->where('type', 2);
                    break;
                case 'wechat':
                    $query->where('type', 1);
                    break;
                case 'h5':
                    $query->where('type', 3);
                    break;
                case 'pc':
                    $query->where('type', 0);
                    break;
            }
        }
    }

    /**
     * @param $value
     * @return string
     */
    public function getTypeAttr($value)
    {
        switch ((int)$value) {
            case 0:
                $name = 'PC';
                break;
            case 1:
                $name = '微信公众号';
                break;
            case 2:
                $name = '小程序';
                break;
            case 3:
                $name = 'H5';
                break;
            case 4:
                $name = 'APP';
                break;
        }
        return $name;
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchSexAttr($query, $value)
    {
        if ($value != '') {
            $query->where('sex', $value);
        }
    }

    public function searchIsTouristAttr($query, $value)
    {
        $query->where('is_tourist', $value);
    }
}
