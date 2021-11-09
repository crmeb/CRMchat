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


use think\facade\Log;
use think\swoole\App;

/**
 * Class Ping
 * @package app\webscoket
 */
class Ping
{
    /**
     * @var \think\cache\Driver|\Redis
     */
    protected $redis;


    const CACHE_PINK_KEY = 'ws.p.';


    const CACHE_SET_KEY = 'ws.s';


    /**
     * Ping constructor.
     */
    public function __construct(App $app)
    {
        try {
            $this->redis = $app->cache->store('redis');
            $this->destroy();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @param $id
     * @param $time
     * @param int $timeout
     */
    public function createPing($id, $time, $timeout = 0)
    {
        $this->updateTime($id, $time, $timeout);
        $this->redis->sAdd(self::CACHE_SET_KEY, $id);
    }

    /**
     * @param $id
     * @param $time
     * @param int $timeout
     */
    public function updateTime($id, $time, $timeout = 0)
    {
        $this->redis->set(self::CACHE_PINK_KEY . $id, $time, $timeout);
    }

    /**
     * @param $id
     */
    public function removePing($id)
    {
        $this->redis->del(self::CACHE_PINK_KEY . $id);
        $this->redis->del(self::CACHE_SET_KEY, $id);
    }

    /**
     * @param $id
     * @return bool|string
     */
    public function getLastTime($id)
    {
        try {
            return $this->redis->get(self::CACHE_PINK_KEY . $id);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }

    }

    /**
     */
    public function destroy()
    {
        $members = $this->redis->sMembers(self::CACHE_SET_KEY) ?: [];
        foreach ($members as $k => $member) {
            $members[$k] = self::CACHE_PINK_KEY . $member;
        }
        if (count($members))
            $this->redis->sRem(self::CACHE_SET_KEY, ...$members);
    }
}
