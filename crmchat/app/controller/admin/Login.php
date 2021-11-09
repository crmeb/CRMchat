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
namespace app\controller\admin;


use app\validate\system\SystemAdminValidata;
use crmeb\utils\Captcha;
use app\services\system\admin\SystemAdminServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use think\Response;

/**
 * 后台登陆
 * Class Login
 * @package app\controller\admin
 */
class Login
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * Login constructor.
     * @param SystemAdminServices $services
     */
    public function __construct(SystemAdminServices $services)
    {
        $this->services = $services;
        $this->request  = app()->request;
    }

    /**
     * 验证码
     * @return $this|Response
     */
    public function captcha()
    {
        return app('json')->success(app()->make(Captcha::class)->create([], true));
    }

    /**
     * 登陆
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function login()
    {
        [$account, $password, $imgcode, $key] = $this->request->postMore([
            'account', 'pwd', ['imgcode', ''], ['key', '']
        ], true);

        if (!app()->make(Captcha::class)->checkApi($imgcode, $key)) {
            return app('json')->fail('验证码错误，请重新输入');
        }

        validate(SystemAdminValidata::class)->scene('get')->check(['account' => $account, 'pwd' => $password]);

        return app('json')->success($this->services->login($account, $password, 'admin'));
    }

    /**
     * 获取后台登录页轮播图以及LOGO
     * @return mixed
     */
    public function info()
    {
        return app('json')->success($this->services->getLoginInfo());
    }
}
