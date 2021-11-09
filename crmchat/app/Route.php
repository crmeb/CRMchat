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


use think\route\Url as UrlBuild;

class Route extends \think\Route
{
    /**
     * URL生成 支持路由反射
     * @access public
     * @param string $url 路由地址
     * @param array $vars 参数 ['a'=>'val1', 'b'=>'val2']
     * @return UrlBuild
     */
    public function buildUrl(string $url = '', array $vars = []): UrlBuild
    {
        $str = substr($url, 0, 1);
        if ($str != '/') $url = '/' . $url;
        return parent::buildUrl($url, $vars);
    }
}
