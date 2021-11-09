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

// 事件定义文件
return [
    'bind' => [

    ],

    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'swoole.task' => [\crmeb\listeners\SwooleTaskListen::class],//异步任务 事件
        'swoole.init' => [\crmeb\listeners\InitSwooleLockListen::class],//swoole 初始化事件
        'swoole.start' => [\crmeb\listeners\SwooleStartListen::class],//swoole 启动事件
        'swoole.shutDown' => [\crmeb\listeners\SwooleShutdownListen::class],//swoole 停止事件
        'swoole.workerStart' => [\app\webscoket\SwooleWorkerStart::class],//socket 启动事件
        'swoole.websocket.user' => [\app\webscoket\handler\UserHandler::class],//socket 用户调用事件
        'swoole.websocket.admin' => [\app\webscoket\handler\AdminHandler::class],//socket 后台事件
        'swoole.websocket.kefu' => [\app\webscoket\handler\KefuHandler::class],//socket 客服事件
        'wechat.oauth' => [\app\listener\wechat\Oauth::class], // UserSubscribe 微信授权成功后  wap模块 WapBasic控制器
        'user.login' => [\app\listener\user\Login::class], //用户登陆后事件
        'user.register' => [\app\listener\user\Register::class], //用户注册事件
        'order.create' => [\app\listener\order\Create::class], //订单创建事件
        'order.pay' => [\app\listener\order\Pay::class], //订单支付事件
        'order.delivery' => [\app\listener\order\Delivery::class], //订单发货事件
        'order.take' => [\app\listener\order\Take::class], //订单收货事件
//        'notice.news' => [\app\listener\notice\News::class], //通知->消息事件
        'notice.notice' => [\app\listener\notice\Notice::class], //通知->消息事件
    ],

    'subscribe' => [
        \crmeb\subscribes\TaskSubscribe::class
    ],
];
