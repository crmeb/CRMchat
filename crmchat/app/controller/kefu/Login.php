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

namespace app\controller\kefu;


use app\Request;
use app\services\kefu\LoginServices;
use app\validate\kefu\LoginValidate;
use crmeb\services\CacheService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 登录
 * Class Login
 * @package app\controller\kefu
 */
class Login extends AuthController
{

    /**
     * Login constructor.
     * @param LoginServices $services
     */
    public function __construct(LoginServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 客服登录
     * @param Request $request
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function login(Request $request)
    {
        [$account, $password, $isApp, $clientId] = $request->postMore([
            ['account', ''],
            ['password', ''],
            ['is_app', 0],
            ['client_id', ''],
        ], true);

        validate(LoginValidate::class)->check(['account' => $account, 'password' => $password]);

        $token = $this->services->authLogin($account, $password, (int)$isApp, $clientId);

        return $this->success('登录成功', $token);
    }

    /**
     * 获取登录唯一code
     * @return mixed
     */
    public function getLoginKey()
    {
        $key = md5(time() . uniqid());
        $time = time() + 600;
        CacheService::set($key, 1, 600);
        return $this->success(['key' => $key, 'time' => $time]);
    }

    /**
     * 验证登录
     * @param string $key
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function scanLogin(string $key)
    {
        return $this->success($this->services->scanLogin($key));
    }
}
