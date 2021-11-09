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

namespace crmeb\services\uniPush\options;


use crmeb\exceptions\ApiException;
use crmeb\services\uniPush\OptionsBase;
use think\helper\Str;

/**
 * Class PushMessageOptions
 * @package crmeb\services\uniPush\options
 */
class PushMessageOptions extends OptionsBase
{
    /**
     *
     * @var string
     */
    public $duration = '';

    /**
     * @var string
     */
    public $transmission = '';

    /**
     * @var array
     */
    public $revoke = [];

    /**
     * 通知消息标题，长度 ≤ 50
     * @var string
     */
    public $title;

    /**
     * 通知消息内容，长度 ≤ 256
     * @var string
     */
    public $body = '';

    /**
     * 长文本消息内容，通知消息+长文本样式，与big_image二选一，两个都填写时报错，长度 ≤ 512
     * @var string
     */
    public $bigText = '';

    /**
     * 大图的URL地址，通知消息+大图样式， 与big_text二选一，两个都填写时报错，长度 ≤ 1024
     * @var string
     */
    public $bigImage = '';

    /**
     * 通知的图标名称，包含后缀名（需要在客户端开发时嵌入），如“push.png”，长度 ≤ 64
     * @var string
     */
    public $logo = '';

    /**
     * 通知图标URL地址，长度 ≤ 256
     * @var string
     */
    public $logoUrl = '';

    /**
     * 通知渠道id，长度 ≤ 64
     * @var string
     */
    public $channelId = '';

    /**
     * 通知渠道名称，长度 ≤ 64
     * @var string
     */
    public $channelName = '';

    /**
     * 设置通知渠道重要性（可以控制响铃，震动，浮动，闪灯等等）
     * android8.0以下
     * 0，1，2:无声音，无振动，不浮动
     * 3:有声音，无振动，不浮动
     * 4:有声音，有振动，有浮动
     * android8.0以上
     * 0：无声音，无振动，不显示；
     * 1：无声音，无振动，锁屏不显示，通知栏中被折叠显示，导航栏无logo;
     * 2：无声音，无振动，锁屏和通知栏中都显示，通知不唤醒屏幕;
     * 3：有声音，无振动，锁屏和通知栏中都显示，通知唤醒屏幕;
     * 4：有声音，有振动，亮屏下通知悬浮展示，锁屏通知以默认形式展示且唤醒屏幕;
     * @var int
     */
    public $channelLevel = null;

    /**
     * 点击通知后续动作，
     * 目前支持以下后续动作，
     * intent：打开应用内特定页面，
     * url：打开网页地址，
     * payload：自定义消息内容启动应用，
     * payload_custom：自定义消息内容不启动应用，
     * startapp：打开应用首页，
     * none：纯通知，无后续动作
     * @var string
     */
    public $clickType = '';

    /**
     * 点击通知打开应用特定页面，长度 ≤ 2048;
     * 示例：intent:#Intent;component=你的包名/你要打开的 activity 全路径;S.parm1=value1;S.parm2=value2;end
     * @var string
     */
    public $intent = '';

    /**
     * 点击通知打开链接，长度 ≤ 1024
     * @var string
     */
    public $url = '';

    /**
     * 点击通知加自定义消息，长度 ≤ 3072
     * @var string
     */
    public $payload = '';

    /**
     * 覆盖任务时会使用到该字段，两条消息的notify_id相同，新的消息会覆盖老的消息，范围：0-2147483647
     * @var int
     */
    public $notifyId = null;

    /**
     * 自定义铃声，请填写文件名，不包含后缀名(需要在客户端开发时嵌入)，个推通道下发有效
     * 客户端SDK最低要求 2.14.0.0
     * @var string
     */
    public $ringName = '';

    /**
     * 角标, 必须大于0, 个推通道下发有效
     * 此属性目前仅针对华为 EMUI 4.1 及以上设备有效
     * 角标数字数据会和之前角标数字进行叠加；
     * 举例：角标数字配置1，应用之前角标数为2，发送此角标消息后，应用角标数显示为3。
     * 客户端SDK最低要求 2.14.0.0
     * @var int
     */
    public $badgeAddNum = null;

    /**
     * 需要撤回的taskId
     * @var string
     */
    public $oldTaskId = '';

    /**
     * 在没有找到对应的taskId，是否把对应appId下所有的通知都撤回
     * @var bool
     */
    public $force = false;

    /**
     * @return array
     */
    public function toArray()
    {
        $publicData   = get_object_vars($this);
        $notification = $data = $revoke = [];
        foreach ($publicData as $key => $value) {
            if ($value) {
                $key = Str::snake($key);
                if (in_array($key, ['title', 'body', 'big_text', 'big_image', 'logo', 'logo_url',
                    'channel_id', 'channel_name', 'channel_level', 'click_type', 'intent', 'url',
                    'payload', 'notify_id', 'ring_name', 'badge_add_num'])) {
                    $notification[$key] = $value;
                } else if (in_array($key, ['old_task_id', 'force'])) {
                    $revoke[$key] = $value;
                } else {
                    $data[$key] = $value;
                }
            }
        }
        if ($notification && $revoke) {
            throw new ApiException('消息渠道transmission和revoke不能同时存在');
        }
        if ($notification) {
            $data['notification'] = $notification;
        }
        if ($revoke) {
            $data['revoke'] = $revoke;
        }
        if (!isset($data['notification']) && !isset($data['revoke']) && !isset($data['transmission'])) {
            throw new ApiException('缺少消息发送内容');
        }

        return $data;
    }
}
