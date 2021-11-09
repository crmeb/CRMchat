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

namespace app\webscoket;


use Swoole\Table as SwooleTable;
use think\swoole\Table;

/**
 * 房间管理
 * Class Room
 * @package app\webscoket
 */
class Room
{

    /**
     * 类型 只有kefu和admin有区别
     * @var string
     */
    protected $type = '';

    /**
     * fd前缀
     * @var string
     */
    protected $tableFdPrefix = 'ws_fd_';

    /**
     *
     * @var array
     */
    protected $room = [];

    /**
     * @var \Redis
     */
    protected $cache;

    /**
     *
     */
    const USER_INFO_FD_PRE = 'socket_user_list';

    const TYPE_NAME = 'socket_user_type';

    /**
     * 设置缓存
     * @param $cache
     * @return $this
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * 设置表
     * @param string $type
     * @return $this
     */
    public function type(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 获取表实例
     * @return SwooleTable
     */
    public function getTable(string $tableName = 'user')
    {
        return app()->make(Table::class)->get($tableName);
    }

    /**
     * 添加fd
     * @param string $key fd
     * @param int $uid 用户uid
     * @param int $to_uid 当前聊天人的uid
     * @param int $tourist 是否为游客
     * @return mixed
     */
    public function add(string $key, string $appid, int $userId = 0, int $toUserId = 0, int $tourist = 0)
    {
        $nowkey = $this->tableFdPrefix . $key;
        $data   = [
            'fd'         => $key,
            'is_open'    => 1,
            'appid'      => $appid,
            'type'       => $this->type ?: 'user',
            'user_id'    => $userId,
            'to_user_id' => $toUserId,
            'tourist'    => $tourist
        ];
        $res    = $this->getTable()->set($nowkey, $data);
        $this->cache->sAdd(self::USER_INFO_FD_PRE, $key . '=' . $userId);
        $this->delRepeatUidFd($userId, $key);
        if ($this->type) {
            $this->cache->sAdd(self::TYPE_NAME, $this->type);
            $this->cache->sAdd(self::USER_INFO_FD_PRE . '_' . $this->type, $key . '=' . $userId);
            $this->delRepeatUidFd($userId, $key, [], $this->type);
            $this->type = '';
        }
        return $res;
    }

    /**
     * 删除对应列
     * @param string $type
     * @param $userId
     * @param $fd
     */
    public function deleteFd(string $type, $userId, $fd)
    {
        $this->cache->sRem(self::USER_INFO_FD_PRE, $fd . '=' . $userId);
        $this->cache->sRem(self::USER_INFO_FD_PRE . '_' . $type, $fd . '=' . $userId);
    }

    /**
     * 修改数据
     * @param string $key
     * @param null $field
     * @param null $value
     * @return bool|mixed
     */
    public function update(string $key, $field = null, $value = null)
    {
        $nowkey = $this->tableFdPrefix . $key;
        $res    = true;
        if (is_array($field)) {
            $this->getTable()->del($nowkey);
            $res = $this->getTable()->set($nowkey, $field);
        } else if (!is_array($field) && $value !== null) {
            $data = $this->getTable()->get($nowkey);
            if (!$data) {
                return false;
            }
            $data[$field] = $value;
            $this->getTable()->del($nowkey);
            $res = $this->getTable()->set($nowkey, $data);
        }
        return $res;
    }

    /**
     * 重置
     * @return $this
     */
    public function reset()
    {
        $this->type = $this->typeReset;
        return $this;
    }

    /**
     * 删除
     * @param string $key
     * @return mixed
     */
    public function del(string $key, int $userId = 0)
    {
        $nowkey = $this->tableFdPrefix . $key;
        $this->delsMembers($userId);
        if ($this->type) {
            $this->delsMembers($userId, $this->type);
            $this->type = '';
        }
        return $this->getTable()->del($nowkey);
    }

    /**
     * 删除列
     * @param string $key
     * @param string $type
     * @return bool
     */
    public function delsMembers(int $userId = 0, string $type = '')
    {
        $fds        = $this->getRoomAll($type);
        $removeData = [];
        foreach ($fds as $fd => $id) {
            if ($id == $userId) {
                $removeData[] = $fd . '=' . $id;
            }
        }
        return $removeData && $this->cache->sRem(self::USER_INFO_FD_PRE . ($type ? '_' . $type : ''), ...$removeData);
    }

    /**
     * 是否存在
     * @param string $key
     * @return mixed
     */
    public function exist(string $key)
    {
        return $this->getTable()->exist($this->tableFdPrefix . $key);
    }

    /**
     * 获取fd的所有信息
     * @param string $key
     * @return array|bool|mixed
     */
    public function get(string $key, string $field = null)
    {
        return $this->getTable()->get($this->tableFdPrefix . $key, $field);
    }

    /**
     * uid 获取 fd
     * @param int $userId
     * @return array
     */
    public function uidByFd(int $userId)
    {
        $fds        = $this->getRoomAll($this->type);
        $this->type = '';
        $fd         = [];
        foreach ($fds as $k => $v) {
            if ($v == $userId) {
                $fd[] = $k;
                break;
            }
        }
        return $fd;
    }

    /**
     * 获取客服fd
     * @param int $uid
     * @return bool|mixed|null
     */
    public function kefuUidByFd(int $userId)
    {
        $this->type = 'kefu';
        $fd         = $this->uidByFd($userId);
        $this->type = '';
        return $fd;
    }

    /**
     * fd 获取 uid
     * @param $key
     * @return mixed
     */
    public function fdByUid($key)
    {
        return $this->getTable()->get($this->tableFdPrefix . $key, 'user_id');
    }

    /**
     * 除自己之外的fd
     * @param int $uid
     * @return array
     */
    public function exceptUidFd(int $userId)
    {
        $fds = $this->getRoomAll();
        $key = array_search($userId, $fds);
        unset($fds[$key]);
        return $fds;
    }

    /**
     * 删除群内所有用户信息
     */
    public function remove()
    {
        $all      = $this->cache->sMembers(self::USER_INFO_FD_PRE);
        $res      = $this->cache->sRem(self::USER_INFO_FD_PRE, ...$all);
        $typeList = $this->cache->sMembers(self::TYPE_NAME);
        if ($typeList) {
            foreach ($typeList as $item) {
                if ($item) {
                    $all = $this->cache->sMembers(self::USER_INFO_FD_PRE . '_' . $item);
                    $this->cache->sRem(self::USER_INFO_FD_PRE . '_' . $item, ...$all);
                    unset($all);
                }
            }
        }
        return $res;
    }

    /**
     * 获取房间内的所有人
     * @return array
     */
    public function getRoomAll(string $type = '')
    {
        $fdAll = [];
        $all   = $this->cache->sMembers(self::USER_INFO_FD_PRE . ($type ? '_' . $type : ''));
        foreach ($all as $item) {
            [$fd, $uid] = explode('=', $item);
            $fdAll[(string)$fd] = (int)$uid;
        }
        return $fdAll;
    }

    /**
     * 获取客服所有用户
     * @return array
     */
    public function getKefuRoomAll()
    {
        return $this->cache->sMembers('_ws_kefu');
    }

    /**
     * 删除重复uid的fd
     * @param int $uid
     * @param string $key
     * @param array $fds
     * @param string $type
     * @return bool|int
     */
    public function delRepeatUidFd(int $userId, string $key, array $fds = [], string $type = '')
    {
        if (!$userId) {
            return true;
        }
        $fds    = $fds ?: $this->getRoomAll($type);
        $remove = [];
        foreach ($fds as $fd => $item) {
            if ($userId == $item && $key && $key != $fd) {
                $remove[] = $fd . '=' . $item;
            }
        }
        if (!$remove) {
            return true;
        }
        return $this->cache->sRem(self::USER_INFO_FD_PRE . ($type ? '_' . $type : ''), ...$remove);
    }
}
