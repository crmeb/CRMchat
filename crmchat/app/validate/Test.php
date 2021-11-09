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

namespace app\validate;


use think\Validate;

class Test extends Validate
{
    protected $rule = [
        'real_name' => 'require|test_api',
    ];

    protected $message = [
        'real_name.require'  => '名称必须填写',
        'real_name.test_api' => '名称最多不能超过25个字符',
    ];
}
