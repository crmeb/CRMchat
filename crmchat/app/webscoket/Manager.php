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


use app\services\ApplicationServices;
use crmeb\services\CacheService;
use Swoole\Server;
use Swoole\Websocket\Frame;
use think\Event;
use think\response\Json;
use think\swoole\Websocket;
use think\swoole\websocket\Room;
use app\webscoket\Room as NowRoom;

/**
 * Class Manager
 * @package app\webscoket
 */
class Manager extends Websocket
{

    /**
     * @var Ping
     */
    protected $pingService;

    /**
     * @var int
     */
    protected $cache_timeout;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var \Redis
     */
    protected $cache;

    /**
     * @var NowRoom
     */
    protected $nowRoom;

    const USER_TYPE = ['admin', 'user', 'kefu'];

    /**
     * Manager constructor.
     * @param Server $server
     * @param Room $room
     * @param Event $event
     * @param Response $response
     * @param Ping $ping
     * @param \app\webscoket\Room $nowRoom
     */
    public function __construct(\think\App $app, Server $server, Room $room, Event $event, Response $response, Ping $ping, NowRoom $nowRoom)
    {
        parent::__construct($app, $server, $room, $event);
        $this->response = $response;
        $this->pingService = $ping;
        $this->nowRoom = $nowRoom;
        $this->cache = CacheService::redisHandler();
        $this->nowRoom->setCache($this->cache);
        $this->cache_timeout = intval(app()->config->get('swoole.websocket.ping_timeout', 60000) / 1000) + 2;
    }

    /**
     * @param int $fd
     * @param Request $request
     * @return mixed
     */
    public function onOpen($fd, \think\Request $request)
    {
        $type = $request->get('type');
        $token = $request->get('token');
        $app = $request->get('app', 0);
        if (!$token || !in_array($type, self::USER_TYPE)) {
            return $this->server->close($fd);
        }

        $this->nowRoom->type($type);

        try {
            $data = $this->exec($type, 'login', [$fd, $request->get('form_type', null), ['token' => $token, 'app' => $app], $this->response])->getData();
        } catch (\Throwable $e) {
            return $this->server->close($fd);
        }

        if ($data['status'] != 200) {
            return $this->server->close($fd);
        }

        $uid = $data['data']['uid'] ?? 0;

        if ($uid) {
            $this->login($type, $uid, $fd);
        }

        $this->nowRoom->add($fd, $data['data']['appid'] ?? '', $uid);
        $this->pingService->createPing($fd, time(), $this->cache_timeout);
        $this->send($fd, $this->response->message('ping', ['now' => time()]));
        return $this->send($fd, $this->response->success($data['data']));
    }

    public function login($type, $uid, $fd)
    {
        $key = '_ws_' . $type;
        $this->cache->sadd($key, $fd);
        $this->cache->sadd($key . $uid, $fd);
        $this->refresh($type, $uid);
    }

    /**
     * 用用户id获取fd
     * @param int $userId
     * @param string $type
     * @return bool|mixed|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getUserIdByFd(int $userId, string $type = '')
    {
        $key = '_ws_' . $type;
        return $this->cache->sMembers($key . $userId);
    }

    /**
     * 刷新key
     * @param $type
     * @param $uid
     */
    public function refresh($type, $uid)
    {
        $key = '_ws_' . $type;
        $this->cache->expire($key, 1800);
        $this->cache->expire($key . $uid, 1800);
    }


    /**
     * @param int $userId
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getUserIdByFds(int $userId)
    {
        $toUserFd = [];
        foreach (['user', 'kefu'] as $type) {
            $toUserFd = array_merge($toUserFd, $this->getUserIdByFd($userId, $type) ?: []);
        }
        return array_merge(array_unique($toUserFd));
    }

    /**
     * @param int $userId
     * @param int $toUserId
     * @param string $field
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function updateTabelField(int $userId, int $toUserId, string $field = 'to_user_id')
    {
        $fds = $this->getUserIdByFds($userId);
        foreach ($fds as $fd) {
            $this->nowRoom->update($fd, $field, $toUserId);
        }
    }

    public function logout($type, $uid, $fd)
    {
        $key = '_ws_' . $type;
        $this->cache->srem($key, $fd);
        $this->cache->srem($key . $uid, $fd);
    }

    /**
     * @param $type
     * @param string $uid
     * @return array
     */
    public static function userFd($type, $uid = '')
    {
        $key = '_ws_' . $type . $uid;
        return CacheService::redisHandler()->smembers($key) ?: [];
    }

    /**
     * 执行事件调度
     * @param $type
     * @param $method
     * @param $result
     * @return null|Json
     */
    protected function exec($type, $method, $result)
    {
        if (!in_array($type, self::USER_TYPE)) {
            return null;
        }
        if (!is_array($result)) {
            return null;
        }
        /** @var Json $response */
        return $this->event->until('swoole.websocket.' . $type, [$method, $result, $this, $this->nowRoom]);
    }

    /**
     * @param Frame $frame
     * @return bool
     */
    public function onMessage(Frame $frame)
    {
        $info = $this->nowRoom->get($frame->fd);
        $result = json_decode($frame->data, true) ?: [];

        if (!isset($result['type']) || !$result['type']) return true;
        $this->refresh($info['type'], $info['user_id']);
        if ($result['type'] == 'ping') {
            return $this->send($frame->fd, $this->response->message('ping', ['now' => time()]));
        }

        $data = $result['data'] ?? [];

        /** @var Response $res */
        $res = $this->exec($info['type'], $result['type'], [$frame->fd, $result['form_type'] ?? null, $data, $this->response]);
        if ($res) return $this->send($frame->fd, $res);
        return true;
    }

    /**
     * 发送文本响应
     * @param $fd
     * @param Json $json
     * @return bool
     */
    public function send($fd, \think\response\Json $json)
    {
        $this->pingService->createPing($fd, time(), $this->cache_timeout);
        return $this->pushing($fd, $json->getData());
    }

    /**
     * 发送
     * @param $data
     * @return bool
     */
    public function pushing($fds, $data, $exclude = null)
    {
        if ($data instanceof \think\response\Json) {
            $data = $data->getData();
        }
        $data = is_array($data) ? json_encode($data) : $data;
        $fds = is_array($fds) ? $fds : [$fds];
        foreach ($fds as $fd) {
            if (!$fd) {
                continue;
            }
            if ($exclude && is_array($exclude) && !in_array($fd, $exclude)) {
                continue;
            } elseif ($exclude && $exclude == $fd) {
                continue;
            }
            $this->server->push($fd, $data);
        }
        return true;
    }

    /**
     * 关闭连接
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($fd, $reactorId)
    {
        $tabfd = (string)$fd;
        if ($this->nowRoom->exist($fd)) {
            $data = $this->nowRoom->get($tabfd);
            $this->nowRoom->deleteFd($data['type'], $data['user_id'], $fd);
            $this->logout($data['type'], $data['user_id'], $fd);
            $this->nowRoom->type($data['type'])->del($tabfd);
            $this->exec($data['type'], 'close', [$fd, null, ['data' => $data], $this->response]);
        }
        $this->pingService->removePing($fd);
    }
}
