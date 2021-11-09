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

namespace crmeb\services\uniPush;


use crmeb\exceptions\ApiException;
use crmeb\services\HttpService;
use crmeb\utils\Collection;
use think\facade\Cache;

/**
 * Class AbstractAPI
 * @package crmeb\services\uniPush
 */
class AbstractAPI
{

    const BASE_URL = 'https://restapi.getui.com/v2/{$appId}';

    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $appKey;

    /**
     * @var string
     */
    protected $masterSecret;

    /**
     * @var HttpService
     */
    protected $http;

    /**
     * @var \think\cache\Driver
     */
    protected $cache;

    /**
     * AbstractAPI constructor.
     * @param HttpService $service
     */
    public function __construct(HttpService $service)
    {
        $this->http = $service;
        $this->cache = Cache::store();
    }

    /**
     * 设置APPID
     * @param string $appId
     * @return $this
     */
    public function setAppId(string $appId)
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * 设置APPkey
     * @param string $appKey
     * @return $this
     */
    public function setAppKey(string $appKey)
    {
        $this->appKey = $appKey;
        return $this;
    }

    /**
     * 设置masterSecret
     * @param string $masterSecret
     * @return $this
     */
    public function setMasterSecret(string $masterSecret)
    {
        $this->masterSecret = $masterSecret;
        return $this;
    }

    /**
     * 返回API地址
     * @param string $path
     * @return string
     */
    protected function url(string $path = '')
    {
        $baseUrl = $this->resolvBaseUrl();
        $base = strstr($path, 'http') === false;
        return ($base ? $baseUrl : '') . ($path ? $base ? '/' . $path : $path : '');
    }

    /**
     * @return string|string[]
     */
    protected function resolvBaseUrl()
    {
        return str_replace('{$appId}', $this->appId, self::BASE_URL);
    }


    /**
     * 获取签名
     * @return string
     */
    protected function getSign(float $msectime)
    {
        return hash('sha256', $this->appKey . $msectime . $this->masterSecret);
    }

    /**
     * 获取毫秒值
     * @return float
     */
    protected function getMsectime()
    {
        list($usec, $sec) = explode(" ", microtime());
        $time = ($sec . substr($usec, 2, 3));
        return $time;
    }

    /**
     * 以JSON形式发送post请求
     * @param string $url
     * @param array $data
     * @param array $header
     * @return Collection
     * @throws \Exception
     */
    public function parseJSON(string $url, array $data, array $header = [])
    {
        return $this->curl($url, $data, $header, 'post', true);
    }

    /**
     * curl请求
     * @param string $url
     * @param array $data
     * @param array $header
     * @param string|null $method
     * @return Collection
     * @throws \Exception
     */
    protected function curl(string $url, array $data, array $header = [], string $method = null, bool $json = false)
    {
        $header = $json ? array_merge(['content-type:application/json'], $header) : $header;
        $method = $method ?: 'post';
        $response = $this->http->request($this->url($url), $method, $json ? json_encode($data) : $data, $header, 15, true);
        $response = json_decode($response, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception('Failed to parse JSON: :' . json_last_error_msg());
        }
        return new Collection($response);
    }

    /**
     * 获取token
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function getToken()
    {
        $name = 'UNI_PUSH_TOKEN_' . $this->appId;
        $token = $this->cache->get($name);
        if (!$token) {
            $msectime = $this->getMsectime();
            $response = $this->parseJSON('auth', [
                'sign' => $this->getSign($msectime),
                'timestamp' => $msectime,
                'appkey' => $this->appKey
            ]);
            $data = $response->data;
            if (!isset($data['token'])) {
                throw new ApiException('获取token失败');
            }
            if ($data['token']) {
                $this->cache->set($name, $data['token'], 3600);
                $token = $data['token'];
            }
        }
        return $token;
    }

    /**
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAuthToken()
    {
        $name = 'UNI_PUSH_TOKEN';
        $nameAppId = 'UNI_PUSH_APPID';
        $token = $this->cache->get($name);
        $appId = $this->cache->get($nameAppId);
        if (!$token || !$appId) {
            $data = $this->curl('https://store.crmeb.net/api/open/token', [
                'host' => request()->host(),
                'version' => get_crmeb_version()
            ]);
            $data = $data->data;
            if (!isset($data['token'])) {
                throw new ApiException('获取token失败');
            }
            if ($data['token']) {
                $this->cache->set($name, $data['token'], 3600);
                $this->cache->set($nameAppId, $data['appId'], 3600);
                $token = $data['token'];
            }
        }
        $this->appId = $appId;
        return $token;
    }

    /**
     * post请求
     * @param string $url
     * @param array $data
     * @return Collection
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function parsePost(string $url, array $data)
    {
        return $this->http($url, $data);
    }

    /**
     * @param string $url
     * @param array $data
     * @return Collection
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function parseGet(string $url, array $data = [])
    {
        return $this->http($url, $data, 'get');
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @return Collection
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function http(string $url, array $data, string $method = 'post')
    {
//        $token  = $this->getToken();//本地
        $token = $this->getAuthToken();//远程
        $header = [
            'token:' . $token
        ];
        return $this->curl($url, $data, $header, $method, true);
    }
}
