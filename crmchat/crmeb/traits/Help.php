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

namespace crmeb\traits;

/**
 * Trait Help
 * @package crmeb\traits
 */
trait Help
{

    /**
     * 成功返回
     * @param string $msg
     * @param array|null $data
     * @return mixed
     */
    protected function success($msg = 'ok', ?array $data = [])
    {
        return app('json')->success($msg, $data);
    }

    /**
     * 错误返回
     * @param string $msg
     * @param array|null $data
     * @return mixed
     */
    protected function fail($msg = 'error', ?array $data = [])
    {
        return app('json')->fail($msg, $data);
    }

}
