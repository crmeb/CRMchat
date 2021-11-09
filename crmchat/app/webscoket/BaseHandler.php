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

use app\jobs\ServiceTransfer;
use app\jobs\UniPush;
use app\services\chat\ChatServiceAuxiliaryServices;
use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatServiceServices;
use app\services\chat\ChatUserServices;
use crmeb\services\SwooleTaskService;
use crmeb\utils\Arr;
use Swoole\Timer;
use think\exception\ValidateException;
use think\facade\Log;

/**
 * socket 事件基础类
 * Class BaseHandler
 * @package app\webscoket
 */
abstract class BaseHandler
{

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Room
     */
    protected $room;

    /**
     * @var int
     */
    protected $fd;

    /**
     * 用户聊天端
     * @var int|null
     */
    protected $formType;

    /**
     * 登陆
     * @param array $data
     * @param Response $response
     * @return mixed
     */
    abstract public function login(array $data = [], Response $response);

    /**
     * 事件入口
     * @param $event
     * @return |null
     */
    public function handle($event)
    {
        [$method, $result, $manager, $room] = $event;
        $this->manager = $manager;
        $this->room = $room;
        $this->fd = array_shift($result);
        $this->formType = array_shift($result);
        if (method_exists($this, $method)) {
            return $this->{$method}(...$result);
        } else {
            Log::error('socket 回调事件' . $method . '不存在,消息内容为:' . json_encode($result));
            return null;
        }
    }

    /**
     * 聊天事件
     * @param array $data
     * @param Response $response
     * @return bool|\think\response\Json|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function chat(array $data = [], Response $response)
    {
        $user = $this->room->get($this->fd);
        if (!$user) {
            return $response->fail('聊天用户不存在');
        }
        $appId = $user['appid'];
        $to_user_id = $data['to_user_id'] ?? 0;
        $msn_type = $data['type'] ?? 0;
        $msn = $data['msn'] ?? '';
        $formType = $this->formType ?? null;
        $userId = $user['user_id'];
        $other = $data['other'] ?? [];
        $guid = $data['guid'] ?? 0;
        if (!$to_user_id) {
            return $response->message('err_tip', ['msg' => '用户不存在']);
        }
        if ($to_user_id == $userId) {
            return $response->message('err_tip', ['msg' => '不能和自己聊天']);
        }

        /** @var ChatServiceDialogueRecordServices $logServices */
        $logServices = app()->make(ChatServiceDialogueRecordServices::class);
        if (!in_array($msn_type, ChatServiceDialogueRecordServices::MSN_TYPE)) {
            return $response->message('err_tip', ['msg' => '格式错误']);
        }
        $msn = trim(strip_tags(str_replace(["\n", "\t", "\r", "&nbsp;"], '', htmlspecialchars_decode($msn))));
        $data = compact('to_user_id', 'msn_type', 'msn');
        $data['add_time'] = time();
        $data['appid'] = $appId;
        $data['user_id'] = $userId;
        $data['guid'] = $guid;
        $data['is_send'] = 1;

        $toUserFd = $this->manager->getUserIdByFds($to_user_id);

        $toUser = ['to_user_id' => -1];
        $fremaData = [];
        foreach ($toUserFd as $value) {
            if ($frem = $this->room->get($value)) {
                $fremaData[] = $frem;
                if ($frem['to_user_id'] == $userId) {
                    $toUser = $frem;
                }
            }
        }
        //是否在线
        $userOnline = count($fremaData) ? 1 : 0;
        //是否和当前用户对话
        $online = $toUserFd && $toUser && $toUser['to_user_id'] == $userId;
        $data['type'] = $online ? 1 : 0;
        if (in_array($msn_type, [5, 6])) {
            $data['other'] = json_encode($other);
        } else {
            $data['other'] = '';
        }
        $data = $logServices->save($data);
        $data = $data->toArray();
        $data['_add_time'] = $data['add_time'];
        $data['add_time'] = strtotime($data['add_time']);

        /** @var ChatUserServices $userService */
        $userService = app()->make(ChatUserServices::class);
        $_userInfo = $userService->getUserInfo($data['user_id'], ['nickname', 'avatar', 'version', 'is_tourist', 'online']);
        $isTourist = $_userInfo['is_tourist'];
        $data['nickname'] = $_userInfo['nickname'] ?? '';
        $data['avatar'] = $_userInfo['avatar'] ?? '';

        //用户向客服发送消息，判断当前客服是否在登录中
        /** @var ChatServiceRecordServices $serviceRecored */
        $serviceRecored = app()->make(ChatServiceRecordServices::class);
        $unMessagesCount = $logServices->getMessageNum(['user_id' => $userId, 'to_user_id' => $to_user_id, 'type' => 0]);
        //记录当前用户和他人聊天记录
        $data['recored'] = $serviceRecored->saveRecord(
            $user['appid'],
            $userId,
            $to_user_id,
            $msn,
            $formType ?? 0,
            $msn_type,
            $unMessagesCount,
            (int)$isTourist,
            $data['nickname'],
            $data['avatar'],
            $userOnline
        );
        $data['recored']['nickname'] = isset($_userInfo['version']) && $_userInfo['version'] ? '[' . $_userInfo['version'] . ']' . $data['recored']['nickname'] : $data['recored']['nickname'];
        $data['recored']['_update_time'] = date('Y-m-d H:i', $data['recored']['update_time']);
        /** @var ChatServiceServices $services */
        $services = app()->make(ChatServiceServices::class);
        $kefuInfo = $services->get(['user_id' => $to_user_id, 'appid' => $user['appid']], ['is_backstage', 'online', 'client_id', 'auto_reply']);
        if (!$kefuInfo) {
            $clientId = '';
            $auto_reply = false;
            $kefuOnline = false;
            $isBackstage = false;
        } else {
            $clientId = $kefuInfo->client_id;
            $auto_reply = !!$kefuInfo->auto_reply;
            $kefuOnline = !!$kefuInfo['online'];
            $isBackstage = !!$kefuInfo['is_backstage'];
        }

        //开启自动回复
        if ($auto_reply) {
            $app = app();
            Timer::after(100, function () use ($app, $services, $appId, $to_user_id, $other, $msn_type, $userId, $msn, $response) {
                $data = $services->autoReply($app, $appId, $to_user_id, $userId, $msn, $msn_type, $other);
                if ($data) {
                    //给当前用户自动回复
                    $toUserFd = $this->manager->getUserIdByFds($userId);
                    $this->manager->pushing($toUserFd, $response->message('reply', $data)->getData());
                    //给对方回复消息
                    $toUserFd = $this->manager->getUserIdByFds($to_user_id);
                    $this->manager->pushing($toUserFd, $response->message('chat', $data)->getData());
                }
            });
        }
        $toUserOnline = !!$userService->value(['id' => $to_user_id], 'online');
        //是否在线
        if ($online && $toUserOnline) {
            $this->manager->pushing($toUserFd, $response->message('reply', $data)->getData());
        } else {
            //用户在线，可是没有和当前用户进行聊天，给当前用户发送未读条数
            if ($toUserFd && $toUser['to_user_id'] != $userId && $isBackstage && $kefuOnline) {
                $data['recored']['nickname'] = $_userInfo['nickname'];
                $data['recored']['avatar'] = $_userInfo['avatar'];

                $data['recored']['online'] = $userOnline;
                $allUnMessagesCount = $logServices->getMessageNum([
                    'appid' => $user['appid'],
                    'to_user_id' => $to_user_id,
                    'type' => 0
                ]);

                $this->manager->pushing($toUserFd, $response->message('mssage_num', [
                    'user_id' => $userId,
                    'num' => $unMessagesCount,//某个用户的未读条数
                    'allNum' => $allUnMessagesCount,//总未读条数
                    'recored' => $data['recored']
                ])->getData());
            } else if ($kefuOnline && $clientId && $kefuInfo) {
                //客服不在线,但是客服在app登录了,状态保持在线,发送app推送消息
                UniPush::dispatch([
                    ['nickname' => $data['nickname'], 'to_user_id' => $to_user_id, 'user_id' => $userId, 'appid' => $appId],
                    $clientId,
                    [
                        'content' => $msn,
                        'msn_type' => $data['msn_type'],
                        'other' => is_string($data['other']) ?
                            json_decode($data['other'], true) :
                            $data['other'],
                    ]
                ]);
            } else if (!$kefuOnline && $kefuInfo) {
                //客服不在线,app端也不在线,自动转接给在线的客服
                $this->authTransfer($response, $data['appid'], $userId, $to_user_id);
            }
        }

        $data['recored'] = $serviceRecored->get(['user_id' => $userId, 'to_user_id' => $to_user_id], ['*'], ['user']);
        if ($data['recored']) {
            $data['recored'] = $data['recored']->toArray();
            $data['recored']['_update_time'] = date('Y-m-d H:i', $data['recored']['update_time']);
            $data['recored']['nickname'] = isset($data['recored']['user']['version']) && $data['recored']['user']['version'] ? '[' . $data['recored']['user']['version'] . ']' . $data['recored']['nickname'] : $data['recored']['nickname'];
        }
        return $response->message('chat', $data);
    }

    /**
     * 聊天自动转接
     * @param Response $response
     * @param string $appid
     * @param $userId
     * @param $kfuUserId
     * @return bool
     */
    protected function authTransfer(Response $response, string $appid, $userId, $kfuUserId)
    {
        /** @var ChatServiceServices $services */
        $services = app()->make(ChatServiceServices::class);
        //客服不在线,app端也不在线,自动转接给在线的客服
        $kefuUserInfo = $services->getColumn(['online' => 1, 'appid' => $appid], 'user_id,id');
        if (!$kefuUserInfo) {
            return $this->manager->pushing($userId, $response->message('kefu_logout', [
                'user_id' => $kfuUserId,
                'online' => 0
            ])->getData());
        }
        $userIds = array_column($kefuUserInfo, 'user_id');
        mt_srand();
        $kefuToUserId = $userIds[array_rand($userIds)] ?? 0;

        /** @var ChatServiceDialogueRecordServices $service */
        $service = app()->make(ChatServiceDialogueRecordServices::class);
        $where = ['chat' => [$kfuUserId, $userId]];
        $messageData = $service->getMessageOne($where);
        $messageData = $messageData ? $messageData->toArray() : [];

        try {
            /** @var ChatServiceRecordServices $serviceRecord */
            $serviceRecord = app()->make(ChatServiceRecordServices::class);
            $info = $serviceRecord->get(['user_id' => $kfuUserId, 'to_user_id' => $userId, 'appid' => $appid], ['id', 'user_id', 'to_user_id', 'type', 'message_type', 'is_tourist', 'avatar', 'nickname']);
            /** @var ChatServiceAuxiliaryServices $transfeerService */
            $transfeerService = app()->make(ChatServiceAuxiliaryServices::class);
            $record = $service->transaction(function () use ($info, $serviceRecord, $messageData, $appid, $transfeerService, $service, $kfuUserId, $userId, $kefuToUserId) {
                $record = $serviceRecord->saveRecord(
                    $appid,
                    $userId,
                    $kefuToUserId,
                    $messageData['msn'] ?? '',
                    $info['type'] ?? 1,
                    $messageData['message_type'] ?? 1,
                    0,
                    (int)($info['is_tourist'] ?? 0),
                    $info['nickname'] ?? "",
                    $info['avatar'] ?? ''
                );
                $res = $serviceRecord->delete(['user_id' => $kfuUserId, 'to_user_id' => $userId, 'appid' => $appid]);
                $res = $res && $serviceRecord->delete(['user_id' => $userId, 'to_user_id' => $kfuUserId, 'appid' => $appid]);
                $transfeerService->saveAuxliary([
                    'binding_id' => $userId,
                    'relation_id' => $kefuToUserId,
                    'appid' => $appid
                ]);
                if (!$record && !$res) {
                    throw new ValidateException('转接客服失败');
                }
                return $record;
            });

            $keufInfo = $services->get(['user_id' => $kfuUserId], ['avatar', 'nickname']);
            if ($keufInfo) {
                $keufInfo = $keufInfo->toArray();
            } else {
                $keufInfo = (object)[];
            }
            /** @var ChatUserServices $userService */
            $userService = app()->make(ChatUserServices::class);
            $version = $userService->value(['id' => $userId], 'version');
            if ($version) {
                $record['nickname'] = '[' . $version . ']' . $record['nickname'];
            }
            //给转接的客服发送消息通知
            $kefuToUserIdFd = $this->manager->getUserIdByFds($kefuToUserId);
            $this->manager->pushing($kefuToUserIdFd, $response->message('transfer', [
                'recored' => $record,
                'kefuInfo' => $keufInfo
            ]));
            //给当前客服发送此用户已被转接走的消息通知
            $kefuUserFd = $this->manager->getUserIdByFds($kfuUserId);
            if ($kefuUserFd) {
                $this->manager->pushing($kefuUserFd, $response->message('rm_transfer', [
                    'recored' => $info->toArray()
                ]));
            }
            //告知用户对接此用户聊天
            $keufToInfo = $services->get(['user_id' => $kefuToUserId], ['avatar', 'nickname']);
            $userIdFd = $this->manager->getUserIdByFds($userId);
            $this->manager->pushing($userIdFd, $response->message('to_transfer', [
                'toUid' => $kefuToUserId,
                'avatar' => $keufToInfo['avatar'] ?? '',
                'nickname' => $keufToInfo['nickname'] ?? ''
            ])->getData());

        } catch (\Exception $e) {
            Log::error('自动转接客服失败:' . $e->getMessage());
        }

    }

    /**
     * 切换用户聊天
     * @param array $data
     * @param Response $response
     * @return \think\response\Json
     */
    public function to_chat(array $data = [], Response $response)
    {
        $toUserId = $data['id'] ?? 0;
        $res = $this->room->get($this->fd);
        if ($res) {
            $userId = $res['user_id'];
            $this->manager->updateTabelField($userId, $toUserId);

            //不是游客进入记录
            if (!$res['tourist'] && $toUserId) {
                /** @var ChatServiceRecordServices $service */
                $service = app()->make(ChatServiceRecordServices::class);
                $service->update(['user_id' => $userId, 'to_user_id' => $toUserId], ['mssage_num' => 0]);
                /** @var ChatServiceDialogueRecordServices $logServices */
                $logServices = app()->make(ChatServiceDialogueRecordServices::class);
                $logServices->update(['user_id' => $toUserId, 'to_user_id' => $userId], ['type' => 1]);
            }
            return $response->message('mssage_num', ['user_id' => $toUserId, 'num' => 0, 'recored' => (object)[]]);
        }
    }

    /**
     * @param array $data
     * @param Response $response
     * @return \think\response\Json
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function open(array $data = [], Response $response)
    {
        $open = $data['open'] ?? 0;
        $res = $this->room->get($this->fd);
        if ($res) {
            $userId = $res['user_id'];
            $this->manager->updateTabelField($userId, $open, 'is_open');
        }
        return $response->message('open', ['message' => 'ok']);
    }

    /**
     * 测试原样返回
     * @param array $data
     * @param Response $response
     * @return bool|\think\response\Json|null
     */
    public function test(array $data = [], Response $response)
    {
        return $response->success($data);
    }

    /**
     * 关闭连接触发
     * @param array $data
     * @param Response $response
     */
    public function close(array $data = [], Response $response)
    {
        $usreId = $data['data']['user_id'] ?? 0;
        $appId = $data['data']['appid'] ?? '';
        if ($usreId) {
            /** @var ChatServiceServices $service */
            $service = app()->make(ChatServiceServices::class);
            if (!$service->value(['appid' => $appId, 'user_id' => $usreId], 'is_app')) {
                $service->update(['user_id' => $usreId], ['online' => 0]);
                /** @var ChatServiceRecordServices $recordSService */
                $recordSService = app()->make(ChatServiceRecordServices::class);
                $recordSService->updateRecord(['to_user_id' => $usreId], ['online' => 0]);
                /** @var ChatUserServices $userService */
                $userService = app()->make(ChatUserServices::class);
                $userService->update(['id' => $usreId], ['online' => 0]);
            }

            $this->manager->pushing($this->room->getKefuRoomAll(), $response->message('user_online', [
                'online' => 0,
                'user_id' => $usreId
            ]), $this->fd);

        }
    }
}
