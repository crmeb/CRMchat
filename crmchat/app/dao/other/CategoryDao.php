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


use app\models\other\Category;
use crmeb\basic\BaseDao;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 分类
 * Class CategoryDao
 * @package app\dao\other
 */
class CategoryDao extends BaseDao
{

    /**
     * 获取当前模型
     * @return string
     */
    protected function setModel(): string
    {
        return Category::class;
    }


    /**
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getCateList(array $where, int $page = 0, int $limit = 0, array $field = ['*'])
    {
        return $this->search($where)->when($page, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->field($field)->order('sort DESC,id DESC')->select()->toArray();
    }

    /**
     * 获取全部分类
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getAll(array $where = [], array $with = [])
    {
        return $this->search($where)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->order('sort DESC')->select()->toArray();
    }
}
