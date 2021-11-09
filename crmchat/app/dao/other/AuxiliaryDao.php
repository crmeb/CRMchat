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

namespace app\dao\other;


use app\models\other\Auxiliary;
use crmeb\basic\BaseDao;

/**
 * 辅助表
 * Class AuxiliaryDao
 * @package app\dao\other
 */
class AuxiliaryDao extends BaseDao
{

    /**
     * 获取当前模型
     * @return string
     */
    protected function setModel(): string
    {
        return Auxiliary::class;
    }
}
