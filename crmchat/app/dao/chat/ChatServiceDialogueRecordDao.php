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

namespace app\dao\chat;


use app\models\chat\ChatServiceDialogueRecord;
use crmeb\basic\BaseDao;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;

/**
 * Class ChatServiceDialogueRecordDao
 * @package app\dao\chat
 */
class ChatServiceDialogueRecordDao extends BaseDao
{

    /**
     * ChatServiceDialogueRecordDao constructor.
     */
    public function __construct()
    {
        //清楚去年的聊天记录
//        $this->removeChat();
    }

    /**
     * 获取当前模型
     * @return string
     */
    protected function setModel(): string
    {
        return ChatServiceDialogueRecord::class;
    }

    /**
     * 获取聊天记录下的uid和to_uid
     * @param int $uid
     * @return mixed
     */
    public function getServiceUserUids(int $uid)
    {
        return $this->search(['user_id' => $uid])->group('user_id,to_user_id')->field(['user_id', 'to_user_id'])->select()->toArray();
    }

    /**
     * 获取聊天记录并分页
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getServiceList(array $where, int $page, int $limit, array $field = ['*'])
    {
        return $this->search($where)->with('user')->field($field)->order('add_time DESC')->page($page, $limit)->select()->toArray();
    }

    /**
     * 获取聊天记录上翻页
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param bool $isUp
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getChatList(array $where, int $limit = 20, int $upperId = 0, array $with = [])
    {
        $toUserId = $where['to_user_id'] ?? 0;
        if (isset($where['to_user_id'])) {
            unset($where['to_user_id']);
        }
        return $this->search($where)->when($upperId, function ($query) use ($upperId, $limit) {
            $query->where('id', '<', $upperId)->limit($limit)->order('id DESC');
        })->when(!$upperId, function ($query) use ($limit) {
            $query->limit($limit)->order('id DESC');
        })->when($toUserId, function ($query) use ($toUserId) {
            $query->where(function ($query) use ($toUserId) {
                $query->where('user_id|to_user_id', $toUserId);
            });
        })->with($with)->select()->toArray();
    }

    /**
     * 获取是否用户的聊天记录条数
     * @param string $appid
     * @param int $userId
     * @return int
     */
    public function chatCount(string $appid, int $userId)
    {
        return $this->getModel()->where(['appid' => $appid])->where(function ($query) use ($userId) {
            $query->where('user_id|to_user_id', $userId);
        })->count();
    }

    /**
     * 清楚去年的聊天记录
     * @return bool
     */
    public function removeChat()
    {
        return $this->search(['time' => 'last year'])->delete();
    }

    /**
     * 清楚上周的游客用户聊天记录
     * @return bool
     */
    public function removeYesterDayChat()
    {
        return $this->search(['time' => 'last week', 'is_tourist' => 1])->delete();
    }


    /**
     * 根据条件获取条数
     * @param array $where
     * @return int
     */
    public function whereByCount(array $where)
    {
        return $this->search(['user_id' => $where['user_id']])->order('id DESC')->where('add_time', '<', time() - 300)->count();
    }

    /**
     * 获取未读消息条数
     * @param array $where
     * @return int
     */
    public function getMessageNum(array $where)
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * 搜索聊天记录
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getMessageList(array $where, int $page = 0, int $limit = 0)
    {
        return $this->search(['chat' => $where['chat']])->when(isset($where['add_time']) && $where['add_time'], function ($query) use ($where) {
            $query->where('add_time', '>', $where['add_time']);
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->select()->toArray();
    }

    /**
     * 获得总条数
     * @param array $where
     * @return int
     */
    public function getMessageCount(array $where)
    {
        return $this->search(['chat' => $where['chat']])->when(isset($where['add_time']) && $where['add_time'], function ($query) use ($where) {
            $query->where('add_time', '>', $where['add_time']);
        })->count();
    }

    /**
     * 获取一条
     * @param array $where
     * @return array|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getMessageOne(array $where)
    {
        return $this->search(['chat' => $where['chat']])->when(isset($where['add_time']) && $where['add_time'], function ($query) use ($where) {
            $query->where('add_time', '>', $where['add_time']);
        })->when(isset($where['appid']) && $where['appid'], function ($query) use ($where) {
            $query->where('appid', $where['appid']);
        })->order('id', 'desc')->find();
    }
}
