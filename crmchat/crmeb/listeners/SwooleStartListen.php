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

namespace crmeb\listeners;


use app\webscoket\Room;
use crmeb\interfaces\ListenerInterface;
use crmeb\services\CacheService;
use think\facade\Log;

/**
 * swoole启动监听
 * Class SwooleStartListen
 * @package crmeb\listeners
 */
class SwooleStartListen implements ListenerInterface
{

    /**
     * 事件执行
     * @param $event
     */
    public function handle($event): void
    {
        try {
            //重启过后重置房间人
            /** @var Room $room */
            $room = app()->make(Room::class);
            $room->setCache(CacheService::redisHandler())->remove();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
