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

namespace app\models\other;

use app\models\chat\user\ChatUserLabel;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * 分类
 * Class Category
 * @package app\models\other
 */
class Category extends BaseModel
{


    use ModelTrait;

    /**
     * 表名
     * @var string
     */
    protected $name = 'category';

    /**
     * 主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * @return \think\model\relation\HasMany
     */
    public function label()
    {
        return $this->hasMany(ChatUserLabel::class, 'cate_id', 'id');
    }

    /**
     * 搜索分类名称
     * @param Model $query
     * @param $value
     */
    public function searchNameAttr($query, $value)
    {
        $query->whereLike('name', '%' . $value . '%');
    }

    /**
     *  归属人
     * @param Model $query
     * @param $value
     */
    public function searchOwnerIdAttr($query, $value)
    {
        $query->where('owner_id', $value);
    }

    /**
     *  类型
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }


}
