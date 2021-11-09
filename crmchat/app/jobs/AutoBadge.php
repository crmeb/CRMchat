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

namespace app\jobs;


use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatServiceServices;
use crmeb\basic\BaseJobs;
use crmeb\services\uniPush\PushMessage;
use crmeb\traits\QueueTrait;

/**
 * Class AutoBadge
 * @package app\jobs
 */
class AutoBadge extends BaseJobs
{

    use QueueTrait;

    /**
     * @param $userId
     * @param $appId
     * @return bool
     * @throws \Exception
     */
    public function doJob($userId, $toUserId, $appId)
    {
        /** @var ChatServiceServices $userService */
        $userService = app()->make(ChatServiceServices::class);
        $clientId = $userService->value(['user_id' => $userId, 'appid' => $appId], 'client_id');
        if (!$clientId) {
            return true;
        }
        /** @var ChatServiceRecordServices $logServices */
        $logServices = app()->make(ChatServiceRecordServices::class);
        $allUnMessagesCount = $logServices->sum([
            'appid' => $appId,
            'user_id' => $userId,
        ], 'mssage_num');
        /** @var PushMessage $pushMessage */
        $pushMessage = app()->make(PushMessage::class);
        $pushMessage->userBadge([$clientId], $allUnMessagesCount == 0 ? '0' : '-' . $allUnMessagesCount);
        if ($toUserId) {
            $logServices->update(['to_user_id' => $userId, 'user_id' => $toUserId], ['type' => 1]);
        }
        return true;
    }
}
