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

namespace crmeb\services\uniPush\options;

use crmeb\services\uniPush\OptionsBase;
use think\helper\Str;

/**
 * Class PushOptions
 * @package crmeb\services\uniPush\options
 */
class PushOptions extends OptionsBase
{

    /**
     * @var string
     */
    public $requestId;

    /**
     *
     * @var array
     */
    public $settings = [];

    /**
     * @var array
     */
    public $audience = [];

    /**
     * @var array
     */
    public $pushMessage = [];

    /**
     * @var array
     */
    public $pushChannel = [];

    /**
     * PushOptions constructor.
     * @param string $requestId
     * @param array $settings
     * @param array $audience
     * @param array $pushMessage
     */
    public function __construct(string $requestId = '', array $settings = [], array $audience = [], array $pushMessage = [])
    {
        $this->requestId   = $requestId;
        $this->settings    = $settings;
        $this->audience    = $audience;
        $this->pushMessage = $pushMessage;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $publicData = get_object_vars($this);
        $data       = [];
        foreach ($publicData as $key => $value) {
            if ($value) {
                $data[Str::snake($key)] = $value;
            }
        }
        return $data;
    }

    /**
     * @param PushMessageOptions $options
     * @return $this
     */
    public function setPushMessage(PushMessageOptions $options)
    {
        $this->pushMessage = $options->toArray();
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setPushChannel(AndroidOptions $androidOptions, IosOptions $iosOptions)
    {
        $this->pushChannel = [
            'ios'     => $iosOptions->toArray(),
            'android' => $androidOptions->toArray()
        ];
        return $this;
    }

    /**
     * @param mixed ...$clientId
     * @return $this
     */
    public function setAudience(...$clientId)
    {
        $this->audience = ['cid' => $clientId];
        return $this;
    }

}
