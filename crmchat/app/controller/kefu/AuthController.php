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


use crmeb\basic\BaseModel;
use crmeb\traits\Help;
use think\App;
use think\Request;

/**
 * Class AuthController
 * @package app\controller\kefu
 */
abstract class AuthController
{
    use Help;

    /**
     * @var int
     */
    protected $kefuId;

    /**
     * @var BaseModel
     */
    protected $kefuInfo;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var object|App
     */
    protected $app;

    /**
     * @var object
     */
    protected $services;

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->request = app()->request;
        $this->app     = app();
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
        if ($this->request->hasMacro('kefuId')) {
            $this->kefuId = $this->request->kefuId();
        }
        if ($this->request->hasMacro('kefuInfo')) {
            $this->kefuInfo = $this->request->kefuInfo();
        }
    }
}
