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

namespace app\webscoket\handler;


use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatServiceServices;
use app\services\chat\ChatUserServices;
use app\services\kefu\LoginServices;
use app\webscoket\BaseHandler;
use app\webscoket\Response;
use crmeb\exceptions\AuthException;
use Psr\SimpleCache\InvalidArgumentException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

/**
 * Class KefuHandler
 * @package app\webscoket\handler
 */
class KefuHandler extends BaseHandler
{

    /**
     * @param array $data
     * @param Response $response
     * @return bool|mixed|Json|null
     * @throws InvalidArgumentException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function login(array $data = [], Response $response)
    {
        if (!isset($data['token']) || !$token = $data['token']) {
            return $response->fail('授权失败!');
        }

        try {
            /** @var LoginServices $services */
            $services = app()->make(LoginServices::class);
            $kefuInfo = $services->parseToken($token);
        } catch (AuthException $e) {
            return $response->fail($e->getMessage());
        }

        /** @var ChatUserServices $userService */
        $userService = app()->make(ChatUserServices::class);
        $user = $userService->get($kefuInfo['user_id'], ['id', 'nickname', 'appid']);
        if (!isset($user['id'])) {
            return $response->fail('您登录的客服用户不存在');
        }
        /** @var ChatServiceServices $service */
        $service = app()->make(ChatServiceServices::class);
        //不是在app端把在后台替换到前台
        if ((isset($data['app']) && !$data['app']) || !isset($data['app'])) {
            $online = 1;
            $service->update($kefuInfo['id'], ['is_backstage' => 1, 'online' => 1]);
            $user->online = 1;
            $user->save();
        } else {
            $online = $service->value(['id' => $kefuInfo['id']], 'online');
        }

        return $response->success(['uid' => $user['id'], 'online' => (int)$online, 'appid' => $kefuInfo['appid']]);
    }

    /**
     * 兼容前端方法
     * @param array $data
     * @param Response $response
     */
    public function kefu_login(array $data = [], Response $response)
    {
        return $this->login($data, $response);
    }

    /**
     * 上下线
     * @param array $data
     * @param Response $response
     */
    public function online(array $data = [], Response $response)
    {
        $online = $data['online'] ?? 0;
        $user = $this->room->get($this->fd);
        if ($user) {
            /** @var ChatServiceServices $service */
            $service = app()->make(ChatServiceServices::class);
            $service->update(['user_id' => $user['user_id']], ['online' => $online]);
            /** @var ChatUserServices $userService */
            $userService = app()->make(ChatUserServices::class);
            $userService->update(['id' => $user['user_id']], ['online' => $online]);
            /** @var ChatServiceRecordServices $service */
            $service = app()->make(ChatServiceRecordServices::class);
            $service->updateRecord(['to_user_id' => $user['user_id']], ['online' => $online]);
            if ($user['to_user_id']) {
                $fd = $this->room->uidByFd($user['to_user_id']);
                //给当前正在聊天的用户发送上下线消息
                $this->manager->pushing($fd, $response->message('online', [
                    'online' => $online,
                    'user_id' => $user['user_id']
                ])->getData());
            }
        }
    }

    /**
     * 客服转接
     * @param array $data
     * @param Response $response
     * @return Json
     */
    public function transfer(array $data, Response $response)
    {
        $data = $data['data'] ?? [];
        $userId = $data['recored']['user_id'] ?? 0;
        if ($userId && $this->room->uidByFd($userId)) {
            $data['recored']['online'] = 1;
        } else {
            $data['recored']['online'] = 0;
        }
        return $response->message('transfer', $data);
    }

    /**
     * 退出登录
     * @param array $data
     * @param Response $response
     */
    public function logout(array $data = [], Response $response)
    {
        $user = $this->room->get($this->fd);
        $userId = $user['user_id'] ?? 0;
        if ($userId) {
            /** @var ChatServiceServices $service */
            $service = app()->make(ChatServiceServices::class);
            $service->update(['user_id' => $user['user_id']], ['online' => 0]);
            /** @var ChatServiceRecordServices $service */
            $service = app()->make(ChatServiceRecordServices::class);
            $service->updateRecord(['to_user_id' => $userId], ['online' => 0]);
            $this->manager->pushing($this->room->getKefuRoomAll(), $response->message('online', [
                'online' => 0,
                'user_id' => $userId
            ]), $this->fd);
        }
    }
}
