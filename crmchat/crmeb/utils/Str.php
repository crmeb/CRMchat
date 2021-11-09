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

namespace crmeb\utils;

/**
 * 字符串操作帮助类
 * Class Str
 * @package crmeb\utils
 */
class Str
{
    /**
     * @param $action
     * @param $controller
     * @param $module
     * @param $route
     * @return string
     */
    public static function getAuthName(string $action, string $controller, string $module, $route)
    {
        return strtolower($module . '/' . $controller . '/' . $action . '/' . self::paramStr($route));
    }

    /**
     * @param $params
     * @return string
     */
    public static function paramStr($params)
    {
        if (!is_array($params)) $params = json_decode($params, true) ?: [];
        $p = [];
        foreach ($params as $key => $param) {
            $p[] = $key;
            $p[] = $param;
        }
        return implode('/', $p);
    }

    /**
     * 截取中文指定字节
     * @param string $str
     * @param int $utf8len
     * @param string $chaet
     * @param string $file
     * @return string
     */
    public static function substrUTf8($str, $utf8len = 100, $chaet = 'UTF-8', $file = '....')
    {
        if (mb_strlen($str, $chaet) > $utf8len) {
            $str = mb_substr($str, 0, $utf8len, $chaet) . $file;
        }
        return $str;
    }

    /**
     * @param $string
     * @param $length
     * @param string $etc
     * @return string
     */
    public static function truncateUtf8String($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }

    /**
     * @param $subject
     * @param $search
     * @return mixed|string
     */
    public static function after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}
