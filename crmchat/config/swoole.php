<?php

use app\webscoket\Manager;
use Swoole\Table;
use think\swoole\websocket\socketio\Parser;

return is_win() ? [] : [
    'server'     => [
        'host'      => env('SWOOLE_HOST', '0.0.0.0'), // 监听地址
        'port'      => env('SWOOLE_PORT', 20108), // 监听端口
        'mode'      => SWOOLE_PROCESS, // 运行模式 默认为SWOOLE_PROCESS
        'sock_type' => SWOOLE_SOCK_TCP, // sock type 默认为SWOOLE_SOCK_TCP
        'options'   => [
            'pid_file'              => runtime_path() . 'swoole.pid',
            'log_file'              => runtime_path() . 'swoole.log',
            'daemonize'             => true,//是否守护进程
            // Normally this value should be 1~4 times larger according to your cpu cores.
            'reactor_num'           => swoole_cpu_num() * 4,
            'worker_num'            => swoole_cpu_num() * 4,
            'task_worker_num'       => swoole_cpu_num() * 4,
            'task_enable_coroutine' => true,
            'enable_static_handler' => true,
            'document_root'         => root_path('public'),
            'package_max_length'    => 20 * 1024 * 1024,
            'buffer_output_size'    => 10 * 1024 * 1024,
            'socket_buffer_size'    => 128 * 1024 * 1024,
        ],
    ],
    'websocket'  => [
        'enable'        => true,
        'handler'       => Manager::class,
        'parser'        => Parser::class,
        'ping_interval' => 25000,//ping频率
        'ping_timeout'  => 60000,//没有ping后退出毫秒数
        'room'          => [
            'type'  => 'table',
            'table' => [
                'room_rows'   => 4096,
                'room_size'   => 2048,
                'client_rows' => 8192,
                'client_size' => 2048,
            ],
            'redis' => [
                'host'          => '127.0.0.1',
                'port'          => 6379,
                'max_active'    => 10,
                'max_wait_time' => 5,
            ],
        ],
        'listen'        => [],
        'subscribe'     => [],
    ],
    'rpc'        => [
        'server' => [
            'enable'   => false,
            'port'     => 9000,
            'services' => [
            ],
        ],
        'client' => [
        ],
    ],
    'hot_update' => [
        'enable'  => env('APP_DEBUG', false),
        'name'    => ['*.php'],
        'include' => [app_path(), root_path('crmeb')],
        'exclude' => [],
    ],
    //连接池
    'pool'       => [
        'db'    => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
        'cache' => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
        //自定义连接池
    ],
    'coroutine'  => [
        'enable' => false,
        'flags'  => SWOOLE_HOOK_ALL | SWOOLE_HOOK_CURL,
    ],
    'tables'     => [//高性能内存数据库
        'user'   => [
            'size'    => 2048 * 50,
            'columns' => [
                ['name' => 'fd', 'type' => Table::TYPE_INT],
                ['name' => 'type', 'size' => 1024, 'type' => Table::TYPE_STRING],
                ['name' => 'user_id', 'type' => Table::TYPE_INT],
                ['name' => 'to_user_id', 'type' => Table::TYPE_INT],
                ['name' => 'tourist', 'type' => Table::TYPE_INT],
                ['name' => 'is_open', 'type' => Table::TYPE_INT],
                ['name' => 'appid', 'size' => 1024, 'type' => Table::TYPE_STRING],
            ]
        ]
    ],
    //队列
    'queue'      => [
        'enable'  => true,
        'workers' => [
            'CRMEB_CHAT' => [],
        ],
    ],
    //每个worker里需要预加载以共用的实例
    'concretes'  => [],
    //重置器
    'resetters'  => [],
    //每次请求前需要清空的实例
    'instances'  => [],
    //每次请求前需要重新执行的服务
    'services'   => [],
];
