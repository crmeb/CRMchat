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

namespace app\validate\chat;


use think\Validate;

class ChatServiceValidate extends Validate
{

    /**
     * @var string[]
     */
    protected $regex = ['phone' => '/^1[3456789]\d{9}$/'];

    /**
     * @var string[]
     */
    protected $rule = [
        'phone'    => 'require|regex:phone',
        'avatar'   => 'require',
        'nickname' => 'require',
    ];

    /**
     * @var string[]
     */
    protected $message = [
        'phone.require'    => '请输入手机号',
        'phone.regex'      => '手机号格式错误',
        'nickname.require' => '客服昵称不能为空',
        'avatar.require'   => '客服头像不能为空',
    ];
}
