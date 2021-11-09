<?php

namespace PullWord;

use GuzzleHttp\Client;
use PullWord\Exception\HttpException;
use PullWord\Exception\InvalidArgumentException;

abstract class Service
{
    // 服务接口
    protected $uri;

    // 内容
    protected $source = null;

    // 调试模式 默认关闭
    protected $debug = false;

    // 出词概率阈值
    protected $threshold = 0.5;

    // 是否 返回 json 格式 默认返回文本
    protected $json = false;

    private $client;

    public function __construct($source = '')
    {
        $this->source = $source;

        $this->client = new Client([
            'timeout'  => 5
        ]);
    }

    /**
     * 设置抽词内容
     *
     * @param [type] $source
     * @return $this
     */
    public function source($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * 结果返回json格式
     *
     * @return $this
     */
    public function toJson()
    {
        $this->json = true;

        return $this;
    }

    /**
     * 开启阈值
     * 出词概率阈值(0-1之间的小数)，1表示只有100%有把握的词才出
     *
     * @param $value
     * @return $this
     */
    public function threshold($value)
    {
        $this->threshold = floatval($value);

        return $this;
    }

    /**
     * 开启调试模式
     *
     * @return $this
     */
    public function debug($value = true)
    {
        $this->debug = $value;

        return $this;
    }

    /**
     * 获取查询内容
     *
     * @return void
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * 获取 api查询参数
     *
     * @return array
     */
    public function getQuery()
    {
        return [
            'source' => $this->source,
            'param1' => $this->threshold,
            'param2' => $this->debug,
            'json' => $this->json
        ];
    }

    public function getApi()
    {
        return $this->uri;
    }

    /**
     * 获取结果
     * @return mixed
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function get()
    {
        if (!$this->getSource()) {
            throw new InvalidArgumentException("缺少分词内容 source !");
        }

        try {
            $response = $this->client->get($this->getApi(), [
                'query' => http_build_query($this->getQuery()),
            ]);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        return $response->getBody()->getContents();
    }
}
