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

namespace app\models;


use crmeb\basic\BaseModel;
use think\Model;

/**
 * Class ApplicationDao
 * @package app\models
 */
class Application extends BaseModel
{

    /**
     * 表名
     * @var string
     */
    protected $name = 'application';

    /**
     * 主键
     * @var string
     */
    protected $key = 'id';

    /**
     * name搜索
     * @param Model $query
     * @param $value
     */
    public function searchNameLikeAttr($query, $value)
    {
        if ($value) {
            $query->whereLike('name|appid', '%' . $value . '%');
        }
    }

    /**
     * name搜索
     * @param Model $query
     * @param $value
     */
    public function searchNameAttr($query, $value)
    {
        if ($value) {
            $query->where('name', $value);
        }
    }
}
