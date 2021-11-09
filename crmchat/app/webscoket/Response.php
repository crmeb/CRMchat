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

namespace app\webscoket;

use think\response\Json;

/**
 * socket Response
 * Class Response
 * @package app\webscoket
 * @mixin Json
 */
class Response
{

    /**
     *
     * @var Json
     */
    protected $response;

    /**
     * Response constructor.
     */
    public function __construct(Json $response)
    {
        $this->response = $response;
    }

    /**
     * 设置返回参数
     * @param string $type
     * @param array|null $data
     * @param int $status
     * @param array $other
     * @return Json
     */
    public function send(string $type, ?array $data = null, int $status = 200, array $other = [])
    {
        $res = compact('type', 'status');
        if (!is_null($data)) {
            $res['data'] = $data;
        }
        $data = array_merge($res, $other);
        $this->response->data($data);
        return $this->response;
    }

    /**
     * 成功
     * @param string $message
     * @param array|null $data
     * @return bool|null
     */
    public function success($type = 'success', ?array $data = null, int $status = 200)
    {
        if (is_array($type)) {
            $data = $type;
            $type = 'success';
        }
        return $this->send($type, $data, $status);
    }

    /**
     * 失败
     * @param string $message
     * @param array|null $data
     * @return bool|null
     */
    public function fail($type = 'error', ?array $data = null, int $status = 400)
    {
        if (is_array($type)) {
            $data = $type;
            $type = 'error';
        }
        return $this->send($type, $data, $status);
    }

    /**
     * 设置返只有类型没有状态的返回数据
     * @param string $type
     * @param $data
     * @return Json
     */
    public function message(string $type, $data)
    {
        $this->response->data(compact('type', 'data'));
        return $this->response;
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->response, $name], $arguments);
    }

}
