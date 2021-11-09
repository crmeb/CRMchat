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

use app\services\ApplicationServices;
use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatUserServices;
use app\webscoket\BaseHandler;
use app\webscoket\Response;
use think\response\Json;

/**
 * Class UserHandler
 * @package app\webscoket\handler
 */
class UserHandler extends BaseHandler
{

    /**
     * 用户登陆
     * @param array $data
     * @param Response $response
     * @return bool|Json|null
     */
    public function login(array $data = [], Response $response)
    {

        if (!isset($data['token']) || !$token = $data['token']) {
            return $response->fail('缺少应用token!');
        }

        try {
            /** @var ApplicationServices $services */
            $services = app()->make(ApplicationServices::class);
            $application = $services->parseToken($token);
        } catch (\Throwable $e) {
            return $response->fail($e->getMessage());
        }

        $appId = $application['appInfo']['appid'] ?? null;

        if (!$appId) {
            return $response->fail('解析token失败');
        }

        if (!isset($application['user'])) {
            return $response->success('login', ['appid' => $appId]);
        }

        //携带用户信息的连接通知客服人员
        if (isset($application['user'])) {
            $user = $application['user'];
            /** @var ChatServiceRecordServices $service */
            $service = app()->make(ChatServiceRecordServices::class);
            $service->updateRecord(['to_user_id' => $user['id']], ['online' => 1, 'type' => $res['form_type'] ?? 1]);
            //给所有在线客服人员发送当前用户上线消息
            $this->manager->pushing($this->room->getKefuRoomAll(), $response->message('user_online', [
                'user_id' => $user['id'],
                'online' => 1
            ])->getData(), $this->fd);
        }

        return $response->success('login', ['appid' => $appId, 'uid' => $user['id'] ?? 0]);
    }

    /**
     * 注册或者更新用户
     * @param array $data
     * @param Response $response
     * @return bool|Json|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function user(array $data = [], Response $response)
    {
        $frame = $this->room->get($this->fd);
        if (!$frame) {
            return $response->fail('连接不存在');
        }
        $appId = $frame['appid'];
        $update = $data['update'] ?? 0;
        $user = [
            'uid' => $data['uid'] ?? 0,
            'nickname' => $data['nickname'] ?? '',
            'avatar' => $data['avatar'] ?? '',
            'phone' => $data['phone'] ?? '',
            'openid' => $data['openid'] ?? '',
            'type' => $data['type'] ?? 0,
        ];
        if (!$user['uid']) {
            return $response->fail('缺少UID');
        }

        try {
            /** @var ApplicationServices $services */
            $services = app()->make(ApplicationServices::class);
            $userInfo = $services->createUser($appId, $user);
        } catch (\Throwable $e) {
            return $response->fail($e->getMessage());
        }

        $this->room->update($this->fd, 'user_id', $userInfo['id']);

        /** @var ChatServiceRecordServices $service */
        $service = app()->make(ChatServiceRecordServices::class);
        $service->update(['to_user_id' => $userInfo['id']], ['online' => 1]);
        /** @var ChatUserServices $userService */
        $userService = app()->make(ChatUserServices::class);
        $userService->update(['id' => $userInfo['id']], ['online' => 1]);

        $toUserId = $data['to_user_id'] ?? 0;
        if ($toUserId) {
            $userId = $userInfo['id'];
            $this->room->update($this->fd, 'to_user_id', $toUserId);
            //不是游客进入记录
            if (!$frame['tourist']) {
                $service->update(['user_id' => $userId, 'to_user_id' => $toUserId], ['mssage_num' => 0]);
                /** @var ChatServiceDialogueRecordServices $logServices */
                $logServices = app()->make(ChatServiceDialogueRecordServices::class);
                $logServices->update(['user_id' => $toUserId, 'to_user_id' => $userId], ['type' => 1]);
            }


            $this->manager->pushing($this->fd, $response->message('mssage_num', ['user_id' => $toUserId, 'num' => 0, 'recored' => (object)[]])->getData());
        }
        $this->manager->login($frame['type'], $userInfo['id'], $this->fd);

        $this->manager->pushing($this->room->getKefuRoomAll(), $response->message('user_online', [
            'user_id' => $userInfo['id'],
            'online' => 1
        ])->getData(), $this->fd);

        return $response->success('user');
    }

}
