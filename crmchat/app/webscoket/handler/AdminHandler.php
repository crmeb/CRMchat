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


namespace app\webscoket\handler;

use app\services\system\admin\AdminAuthServices;
use app\webscoket\BaseHandler;
use app\webscoket\Response;
use crmeb\exceptions\AuthException;
use Psr\SimpleCache\InvalidArgumentException;
use think\response\Json;

/**
 * Class AdminHandler
 * @package app\webscoket\handler
 */
class AdminHandler extends BaseHandler
{

    /**
     * 后台登陆
     * @param array $data
     * @param Response $response
     * @return bool|mixed|Json|null
     * @throws InvalidArgumentException
     */
    public function login(array $data = [], Response $response)
    {
        if (!isset($data['token']) || !$token = $data['token']) {
            return $response->fail('授权失败!');
        }

        try {
            /** @var AdminAuthServices $adminAuthService */
            $adminAuthService = app()->make(AdminAuthServices::class);
            $authInfo         = $adminAuthService->parseToken($token);
        } catch (AuthException $e) {
            return $response->fail($e->getMessage());
        }

        if (!$authInfo || !isset($authInfo['id'])) {
            return $response->fail('授权失败!');
        }

        return $response->success(['uid' => $authInfo['id']]);
    }

}
