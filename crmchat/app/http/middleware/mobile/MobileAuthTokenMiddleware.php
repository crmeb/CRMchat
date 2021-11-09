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

namespace app\http\middleware\mobile;


use app\Request;
use app\services\ApplicationServices;
use crmeb\interfaces\MiddlewareInterface;
use Psr\SimpleCache\InvalidArgumentException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Config;

/**
 * Class MobileAuthTokenMiddleware
 * @package app\http\middleware\mobile
 */
class MobileAuthTokenMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws InvalidArgumentException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = trim(ltrim($request->header(Config::get('cookie.token_name', 'Authori-zation')), 'Bearer'));
        /** @var ApplicationServices $services */
        $services = app()->make(ApplicationServices::class);
        $appInfo  = $services->parseToken($token);

        Request::macro('appId', function () use (&$appInfo) {
            return $appInfo['appInfo']['appid'] ?? null;
        });

        Request::macro('appInfo', function () use (&$appInfo) {
            return $appInfo['appInfo'];
        });

        return $next($request);
    }
}
