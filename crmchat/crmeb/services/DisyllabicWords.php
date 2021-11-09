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

namespace crmeb\services;


use crmeb\utils\Collection;

/**
 * 拆词
 * Class DisyllabicWords
 * @package crmeb\services
 */
class DisyllabicWords
{

    const API_URL = 'http://showapifc.market.alicloudapi.com/sepWord';

    /**
     * @var string
     */
    protected $appCode = '';

    public function __construct(array $config = [])
    {
        $this->appCode = $config['appCode'] ?? '';
    }

    /**
     * 获取分词
     * @param string $text
     * @return array|mixed
     */
    public function getWord(string $text)
    {
        $res = $this->http(self::API_URL, 'post', ['text' => $text], [
            'Authorization:APPCODE ' . $this->appCode,
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8'
        ]);
        $res = new Collection(is_array($res) ? $res : []);
        $showapiResBody = $res->get('showapi_res_body', []);
        return $showapiResBody['list'] ?? [];
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $bodys
     * @return mixed
     */
    public function http(string $url, string $method, array $bodys, array $headers)
    {
        $method = strtoupper($method);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $url, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $bodys = is_array($bodys) ? http_build_query($bodys) : $bodys;
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        [$content, $status] = [curl_exec($curl), curl_getinfo($curl)];
        $content = trim(substr($content, $status['header_size']));
        return json_decode($content, true);
    }
}
