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

namespace app\services\kefu;


use app\dao\chat\ChatServiceDao;
use app\jobs\AutoBadge;
use app\jobs\ServiceTransfer;
use app\services\chat\ChatServiceAuxiliaryServices;
use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatServiceServices;
use app\services\chat\ChatUserServices;
use crmeb\basic\BaseServices;
use crmeb\services\SwooleTaskService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;

/**
 * Class KefuServices
 * @package app\services\kefu
 */
class KefuServices extends BaseServices
{

    /**
     * KefuServices constructor.
     * @param ChatServiceDao $dao
     */
    public function __construct(ChatServiceDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取客服列表
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getServiceList(array $where, array $noId)
    {
        $where['status'] = 1;
        $where['noId'] = $noId;
        $where['online'] = 1;
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getServiceList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 获取聊天记录
     * @param int $userId
     * @param int $toUserId
     * @param int $isUp
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getChatList(int $userId, int $toUserId, int $upperId, string $appId = '')
    {
        /** @var ChatServiceDialogueRecordServices $service */
        $service = app()->make(ChatServiceDialogueRecordServices::class);
        [$page, $limit] = $this->getPageValue();
        AutoBadge::dispatch([$userId, $toUserId, $appId]);
        return array_reverse($service->tidyChat($service->getServiceChatList(['appid' => $appId, 'to_user_id' => $toUserId], $limit, $upperId)));
    }

    /**
     * 转移客服
     * @param string $appid
     * @param int $kfuUserId
     * @param int $userId
     * @param int $kefuToUserId
     * @return bool
     */
    public function setTransfer(string $appid, int $kfuUserId, int $userId, int $kefuToUserId)
    {
        if ($userId === $kefuToUserId) {
            throw new ValidateException('自己不能转接给自己');
        }
        /** @var ChatServiceDialogueRecordServices $service */
        $service = app()->make(ChatServiceDialogueRecordServices::class);
        /** @var ChatServiceAuxiliaryServices $transfeerService */
        $transfeerService = app()->make(ChatServiceAuxiliaryServices::class);
        $where = ['chat' => [$kfuUserId, $userId]];
        $messageData = $service->getMessageOne($where);
        $messageData = $messageData ? $messageData->toArray() : [];
        $record = $this->transaction(function () use ($transfeerService, $where, $messageData, $appid, $service, $kfuUserId, $userId, $kefuToUserId) {
            /** @var ChatServiceRecordServices $serviceRecord */
            $serviceRecord = app()->make(ChatServiceRecordServices::class);
            $info = $serviceRecord->get(['user_id' => $kfuUserId, 'to_user_id' => $userId, 'appid' => $appid], ['id', 'type', 'message_type', 'is_tourist', 'avatar', 'nickname']);
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
        try {
            $keufInfo = $this->dao->get(['user_id' => $kfuUserId], ['avatar', 'nickname']);
            if ($keufInfo) {
                $keufInfo = $keufInfo->toArray();
            } else {
                $keufInfo = (object)[];
            }

            /** @var ChatUserServices $services */
            $services = app()->make(ChatUserServices::class);
            $version = $services->value(['id' => $userId], 'version');
            if ($version) {
                $record['nickname'] = '[' . $version . ']' . $record['nickname'];
            }

            //给转接的客服发送消息通知
            SwooleTaskService::kefu()->type('transfer')->to($kefuToUserId)->data(['recored' => $record, 'kefuInfo' => $keufInfo])->push();
            //告知用户对接此用户聊天
            $keufToInfo = $this->dao->get(['user_id' => $kefuToUserId], ['avatar', 'nickname']);
            SwooleTaskService::user()->type('to_transfer')->to($userId)->data(['toUid' => $kefuToUserId, 'avatar' => $keufToInfo['avatar'] ?? '', 'nickname' => $keufToInfo['nickname'] ?? ''])->push();
        } catch (\Exception $e) {
        }
        return true;
    }
}
