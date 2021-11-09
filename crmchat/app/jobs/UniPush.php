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


use app\services\chat\ChatServiceRecordServices;
use crmeb\basic\BaseJobs;
use crmeb\services\uniPush\options\AndroidOptions;
use crmeb\services\uniPush\options\IosOptions;
use crmeb\services\uniPush\options\PushMessageOptions;
use crmeb\services\uniPush\options\PushOptions;
use crmeb\services\uniPush\PushMessage;
use crmeb\traits\QueueTrait;
use app\services\chat\ChatServiceDialogueRecordServices;
use think\facade\Log;

/**
 * 消息发送队列
 * Class UniPush
 * @package app\jobs
 */
class UniPush extends BaseJobs
{

    use QueueTrait;

    /**
     * @param $userInfo
     * @param $clientId
     * @param $message
     * @return bool
     */
    public function doJob($userInfo, $clientId, $message)
    {
        if (!$clientId) {
            return true;
        }
        if (!$message) {
            return true;
        }
        if (!$userInfo) {
            return true;
        }
        /** @var PushMessage $uniPush */
        $uniPush = app()->make(PushMessage::class);
        $option = new PushOptions();
        $messageOption = new PushMessageOptions();
        $messageOption->title = $userInfo['nickname'];
        $messageOption->badgeAddNum = 1;
        switch ((int)($message['msn_type'] ?? 0)) {
            case ChatServiceDialogueRecordServices::MSN_TYPE_TXT:
                $messageOption->body = $message['content'] ?? '';
                break;
            case ChatServiceDialogueRecordServices::MSN_TYPE_EMOT:
                $messageOption->body = '[表情]';
                break;
            case ChatServiceDialogueRecordServices::MSN_TYPE_IME:
                $messageOption->body = '[图片]';
                break;
            case ChatServiceDialogueRecordServices::MSN_TYPE_VOICE:
                $messageOption->body = '[音频]';
                break;
            case ChatServiceDialogueRecordServices::MSN_TYPE_ORDER:
            case ChatServiceDialogueRecordServices::MSN_TYPE_GOODS:
                $messageOption->body = '[图文]' . ($message['other']['store_name'] ?? '');
                break;
        }
        $url = '/pages/view/customerServer/index?to_user_id=' . $userInfo['user_id'];
        $messageOption->clickType = 'payload';
        $messageOption->payload = json_encode(['url' => $url, 'type' => 'url']);
        $messageOption->channelLevel = 3;
        $option->setAudience($clientId);
        $option->setPushMessage($messageOption);
        $option->pushChannel = [
            'transmission' => json_encode([
                'title' => $messageOption->title,
                'body' => $messageOption->body,
                'url' => $url
            ])
        ];
        /** @var ChatServiceRecordServices $logServices */
        $logServices = app()->make(ChatServiceRecordServices::class);
        $allUnMessagesCount = $logServices->sum([
            'appid' => $userInfo['appid'],
            'user_id' => $userInfo['to_user_id'],
        ], 'mssage_num');
        $ios = new IosOptions();
        $ios->body = $messageOption->body;
        $ios->title = $messageOption->title;
        $ios->payload = $messageOption->payload;
        $ios->autoBadge = (string)$allUnMessagesCount;
        $android = new AndroidOptions();
        $android->body = $messageOption->body;
        $android->title = $messageOption->title;
        $android->payload = $messageOption->payload;
        $android->HWbadgeNum = $ios->autoBadge;
        $option->setPushChannel($android, $ios);
        $res = $uniPush->push($option);
        Log::error(['option' => $option->toArray(), 'res' => $res->toArray()]);
        return true;
    }
}
