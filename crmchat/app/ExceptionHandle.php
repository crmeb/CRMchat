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

namespace app;


use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\exceptions\AuthException;
use crmeb\exceptions\UploadException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Env;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        $massageData = Env::get('app_debug', false) ? [
            'message'  => $e->getMessage(),
            'file'     => $e->getFile(),
            'line'     => $e->getLine(),
            'trace'    => $e->getTrace(),
            'previous' => $e->getPrevious(),
        ] : [];

        // 添加自定义异常处理机制
        if ($e instanceof DbException) {
            return app('json')->fail('数据获取失败', $massageData);
        } elseif ($e instanceof AuthException ||
            $e instanceof ValidateException ||
            $e instanceof ApiException ||
            $e instanceof UploadException ||
            $e instanceof AdminException
        ) {
            return app('json')->make($e->getCode() ?: 400, $e->getMessage(), $massageData);
        }
        return app('json')->make($e->getCode() ?: 400, $e->getMessage(), $massageData);
        // 其他错误交给系统处理
//        return parent::render($request, $e);
    }
}
