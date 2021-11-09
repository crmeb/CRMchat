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

use Spatie\Macroable\Macroable;

/**
 * Class Request
 * @package app
 * @method tokenData() 获取token信息
 * @method user(string $key = null) 获取用户信息
 * @method uid() 获取用户uid
 * @method isAdminLogin() 后台登陆状态
 * @method adminId() 后台管理员id
 * @method adminInfo() 后台管理信息
 * @method kefuId() 客服id
 * @method kefuInfo() 客服信息
 * @method appId() 应用ID
 * @method appInfo() 应用详情
 */
class Request extends \think\Request
{
    use Macroable;

    /**
     * 排序字段
     * @var string[]
     */
    protected $except = [
        'api_url'
    ];

    /**
     * 获取请求的数据
     * @param array $params
     * @param bool $suffix
     * @return array
     */
    public function more(array $params, bool $suffix = false): array
    {
        $p = [];
        $i = 0;
        foreach ($params as $param) {
            if (!is_array($param)) {
                $p[$suffix == true ? $i++ : $param] = $this->filterWord($this->param($param), $param);
            } else {
                if (!isset($param[1])) $param[1] = null;
                if (!isset($param[2])) $param[2] = '';
                if (is_array($param[0])) {
                    $name    = is_array($param[1]) ? $param[0][0] . '/a' : $param[0][0] . '/' . $param[0][1];
                    $keyName = $param[0][0];
                } else {
                    $name    = is_array($param[1]) ? $param[0] . '/a' : $param[0];
                    $keyName = $param[0];
                }
                $p[$suffix == true ? $i++ : (isset($param[3]) ? $param[3] : $keyName)] = $this->filterWord($this->param($name, $param[1], $param[2]), $name);
            }
        }
        return $p;
    }

    /**
     * 过滤接受的参数
     * @param $str
     * @return array|mixed|string|string[]|null
     */
    public function filterWord($str, string $field = null)
    {
        if (!$str) return $str;
        if (strstr($field, '/')) {
            [$field] = explode('/', $field);
        }
        if ($field && in_array($field, $this->except)) {
            return $str;
        }
        // 把数据过滤
        $farr = [
            "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
            "/select|join|where|drop|like|modify|rename|insert|update|table|database|alter|truncate|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
        ];
        if (is_array($str)) {
            foreach ($str as &$v) {
                if (is_array($v)) {
                    foreach ($v as &$vv) {
                        if (!is_array($vv)) $vv = preg_replace($farr, '', $vv);
                    }
                } else {
                    $v = preg_replace($farr, '', $v);
                }
            }
        } else {
            $str = preg_replace($farr, '', $str);
        }
        return $str;
    }


    /**
     * 获取get参数
     * @param array $params
     * @param bool $suffix
     * @return array
     */
    public function getMore(array $params, bool $suffix = false): array
    {
        return $this->more($params, $suffix);
    }

    /**
     * 获取post参数
     * @param array $params
     * @param bool $suffix
     * @return array
     */
    public function postMore(array $params, bool $suffix = false): array
    {
        return $this->more($params, $suffix);
    }

    /**
     * 获取用户访问端
     * @return array|string|null
     */
    public function getFromType()
    {
        return $this->header('Form-type', '');
    }

    /**
     * 当前访问端
     * @param string $terminal
     * @return bool
     */
    public function isTerminal(string $terminal)
    {
        return strtolower($this->getFromType()) === $terminal;
    }

    /**
     * 是否是H5端
     * @return bool
     */
    public function isH5()
    {
        return $this->isTerminal('h5');
    }

    /**
     * 是否是微信端
     * @return bool
     */
    public function isWechat()
    {
        return $this->isTerminal('wechat');
    }

    /**
     * 是否是小程序端
     * @return bool
     */
    public function isRoutine()
    {
        return $this->isTerminal('routine');
    }

    /**
     * 是否是app端
     * @return bool
     */
    public function isApp()
    {
        return $this->isTerminal('app');
    }

    /**
     * 是否是app端
     * @return bool
     */
    public function isPc()
    {
        return $this->isTerminal('pc');
    }

    /**
     * 获取ip
     * @return string
     */
    public function ip(): string
    {
        if ($this->server('HTTP_CLIENT_IP', '')) {
            $ip = $this->server('HTTP_CLIENT_IP', '');
        } elseif ($this->server('HTTP_X_REAL_IP', '')) {
            $ip = $this->server('HTTP_X_REAL_IP', '');
        } elseif ($this->server('HTTP_X_FORWARDED_FOR', '')) {
            $ip  = $this->server('HTTP_X_FORWARDED_FOR', '');
            $ips = explode(',', $ip);
            $ip  = $ips[0];
        } elseif ($this->server('REMOTE_ADDR', '')) {
            $ip = $this->server('REMOTE_ADDR', '');
        } else {
            $ip = '0.0.0.0';
        }
        return $ip;
    }
}
