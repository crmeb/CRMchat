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

namespace crmeb\services;

use Swoole\Server;
use think\App;
use think\facade\Log;

/**
 * 异步任务
 * Class SwooleTaskService
 * @package crmeb\services
 */
class SwooleTaskService
{

    /**
     * @var Server
     */
    protected $server;

    /**
     * 任务类型
     * @var string
     */
    protected $taskType = 'message';

    /**
     * 送达人
     * @var array
     */
    protected $to;

    /**
     * 任务内容
     * @var array
     */
    protected $data = ['type' => null, 'data' => []];

    /**
     * 排除发送人
     * @var array
     */
    protected $except;

    /**
     * 任务区分类型
     * @var string
     */
    protected $type;

    /**
     * @var static
     */
    protected static $instance;

    /**
     * SwooleTaskService constructor.
     * @param string|null $taskType
     * @param App|null $app
     */
    public function __construct(string $taskType = null, App $app = null)
    {
        if ($taskType) {
            $this->taskType = $taskType;
        }
        $this->server = $app ? $app->make('swoole.server') : app('swoole.server');
    }

    /**
     * 任务类型
     * @param string $taskType
     * @return $this
     */
    public function taskType(string $taskType)
    {
        $this->taskType = $taskType;
        return $this;
    }

    /**
     * 消息类型
     * @param string $type
     * @return $this
     */
    public function type(string $type)
    {
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * 设置送达人
     * @param $to
     * @return $this
     */
    public function to($to)
    {
        $this->to = is_array($to) ? $to : func_get_args();
        return $this;
    }

    /**
     * 设置除那个用户不发送
     * @param $except
     * @return $this
     */
    public function except($except)
    {
        $this->except = is_array($except) ? $except : [$except];
        return $this;
    }

    /**
     * 设置参数
     * @param $data
     * @return $this
     */
    public function data($data)
    {
        $this->data['data'] = is_array($data) ? $data : func_get_args();
        return $this;
    }

    /**
     * 执行任务
     */
    public function push()
    {
        try {
            $this->server->task([
                'type' => $this->taskType,
                'data' => [
                    'except' => $this->except,
                    'data' => $this->data,
                    'user_id' => $this->to,
                    'type' => $this->type,
                ]
            ]);
            $this->reset();
        } catch (\Throwable $e) {
            Log::error('任务执行失败,失败原因:' . $e->getMessage());
        }
    }

    /**
     * 实例化
     * @return static
     */
    public static function instance(string $taskType = null, App $app = null)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($taskType, $app);
        }
        return self::$instance;
    }

    /**
     * 客服任务
     * @return SwooleTaskService
     */
    public static function kefu(App $app = null)
    {
        self::instance(null, $app)->type = __FUNCTION__;
        return self::instance();
    }

    /**
     * 后台任务
     * @return SwooleTaskService
     */
    public static function admin(App $app = null)
    {
        self::instance(null, $app)->type = __FUNCTION__;
        return self::instance();
    }

    /**
     * 用户任务
     * @return SwooleTaskService
     */
    public static function user(App $app = null)
    {
        self::instance(null, $app)->type = __FUNCTION__;
        return self::instance();
    }


    /**
     * 重置数据
     * @return $this
     */
    protected function reset()
    {
        $this->taskType = 'message';
        $this->except = null;
        $this->data = ['type' => null, 'data' => []];
        $this->to = null;
        $this->type = null;
        return $this;
    }
}
