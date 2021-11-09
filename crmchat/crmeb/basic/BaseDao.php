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

namespace crmeb\basic;

use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use think\helper\Str;

/**
 * Class BaseDao
 * @package crmeb\basic
 */
abstract class BaseDao
{
    /**
     * 当前表名别名
     * @var string
     */
    protected $alias;

    /**
     * join表别名
     * @var string
     */
    protected $joinAlis;

    /**
     * @var App
     */
    protected $app;


    /**
     * 获取当前模型
     * @return string
     */
    abstract protected function setModel(): string;

    /**
     * 设置join链表模型
     * @return string
     */
    protected function setJoinModel(): string
    {
    }

    /**
     * @param App $app
     * @return $this
     */
    public function setApp(App $app)
    {
        $this->app = $app;
        return $this;
    }

    /**
     * @return App
     */
    protected function getApp()
    {
        return $this->app ?: app();
    }

    /**
     * 读取数据条数
     * @param array $where
     * @return int
     */
    public function count(array $where = []): int
    {
        return $this->search($where)->count();
    }

    /**
     * 查询列表
     * @param array $where
     * @param array|string[] $field
     * @param null $sort
     * @param int $page
     * @param int $limit
     * @param array $with
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getDataList(array $where, array $field = ['*'], $sort = null, int $page = 0, int $limit = 0, array $with = [])
    {
        return $this->search($where)->field($field)->when($sort, function ($query, $sort) {
            if (is_array($sort)) {
                foreach ($sort as $k => $v) {
                    if (is_numeric($k)) {
                        $query->order($k, 'desc');
                    } else {
                        $query->order($k, $v);
                    }
                }
            } else {
                $query->order($sort, 'desc');
            }
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        }, function ($query) use ($limit) {
            if ($limit) {
                $query->limit($limit);
            }
        })->with($with)->select()->toArray();
    }

    /**
     * 获取某些条件总数
     * @param array $where
     */
    public function getCount(array $where)
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * 获取某些条件去重总数
     * @param array $where
     * @param $field
     * @param $search
     */
    public function getDistinctCount(array $where, $field, $search = true)
    {
        if ($search) {
            return $this->search($where)->field('COUNT(distinct(' . $field . ')) as count')->select()->toArray()[0]['count'] ?? 0;
        } else {
            return $this->getModel()->where($where)->field('COUNT(distinct(' . $field . ')) as count')->select()->toArray()[0]['count'] ?? 0;
        }
    }

    /**
     * 获取模型
     * @return BaseModel
     */
    protected function getModel()
    {
        return $this->getApp()->make($this->setModel());
    }

    /**
     * 获取主键
     * @return mixed
     */
    protected function getPk()
    {
        return $this->getModel()->getPk();
    }

    /**
     * 获取一条数据
     * @param int|array $id
     * @param array|null $field
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function get($id, ?array $field = [], ?array $with = [])
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [$this->getPk() => $id];
        }
        return $this->getModel()::where($where)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->field($field ?? ['*'])->find();
    }

    /**
     * 查询一条数据是否存在
     * @param $map
     * @param string $field
     * @return bool 是否存在
     */
    public function be($map, string $field = '')
    {
        if (!is_array($map) && empty($field)) $field = $this->getPk();
        $map = !is_array($map) ? [$field => $map] : $map;
        return 0 < $this->getModel()->where($map)->count();
    }

    /**
     * 根据条件获取一条数据
     * @param array $where
     * @param string|null $field
     * @param array $with
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getOne(array $where, ?string $field = '*', array $with = [])
    {
        $field = explode(',', $field);
        return $this->get($where, $field, $with);
    }

    /**
     * 获取单个字段值
     * @param array $where
     * @param string|null $field
     * @return mixed
     */
    public function value(array $where, ?string $field = '')
    {
        $pk = $this->getPk();
        return $this->getModel()::where($where)->value($field ?: $pk);
    }

    /**
     * 获取某个字段数组
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getColumn(array $where, string $field, string $key = '')
    {
        return $this->getModel()::where($where)->column($field, $key);
    }

    /**
     * 删除
     * @param int|string|array $id
     * @return mixed
     */
    public function delete($id, ?string $key = null)
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()::where($where)->delete();
    }

    /**
     * 更新数据
     * @param int|string|array $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data, ?string $key = null)
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()::update($data, $where);
    }

    /**
     * 批量更新数据
     * @param array $ids
     * @param array $data
     * @param string|null $key
     * @return BaseModel
     */
    public function batchUpdate(array $ids, array $data, ?string $key = null)
    {
        return $this->getModel()::whereIn(is_null($key) ? $this->getPk() : $key, $ids)->update($data);
    }

    /**
     * 插入数据
     * @param array $data
     * @return BaseModel|Model
     */
    public function save(array $data)
    {
        return $this->getModel()::create($data);
    }

    /**
     * 插入数据
     * @param array $data
     * @return mixed
     */
    public function saveAll(array $data)
    {
        return $this->getModel()::insertAll($data);
    }

    /**
     * 获取某个字段内的值
     * @param $value
     * @param string $filed
     * @param string $valueKey
     * @param array|string[] $where
     * @return mixed
     */
    public function getFieldValue($value, string $filed, ?string $valueKey = '', ?array $where = [])
    {
        return $this->getModel()->getFieldValue($value, $filed, $valueKey, $where);
    }

    /**
     * 根据搜索器获取搜索内容
     * @param array $withSearch
     * @param array|null $data
     * @return Model
     */
    protected function withSearchSelect(array $withSearch, ?array $data = [])
    {
        $with = [];
        $respones = new \ReflectionClass($this->setModel());
        foreach ($withSearch as $fieldName) {
            $method = 'search' . Str::studly($fieldName) . 'Attr';
            if ($respones->hasMethod($method)) {
                $with[] = $fieldName;
            }
        }
        return $this->getModel()->withSearch($with, $data);
    }

    /**
     * 搜索
     * @param array $where
     * @return \crmeb\basic\BaseModel|mixed
     */
    protected function search(array $where = [])
    {
        if ($where) {
            return $this->withSearchSelect(array_keys($where), $where);
        } else {
            return $this->getModel();
        }
    }

    /**
     * 求和
     * @param array $where
     * @param string $field
     * @param bool $search
     * @return float
     */
    public function sum(array $where, string $field, bool $search = false)
    {
        if ($search) {
            return $this->search($where)->sum($field);
        } else {
            return $this->getModel()::where($where)->sum($field);
        }
    }

    /**
     * 高精度加法
     * @param int|string $key
     * @param string $incField
     * @param string $inc
     * @param string|null $keyField
     * @param int $acc
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function bcInc($key, string $incField, string $inc, string $keyField = null, int $acc = 2)
    {
        return $this->bc($key, $incField, $inc, $keyField, 1);
    }

    /**
     * 高精度 减法
     * @param int|string $uid id
     * @param string $decField 相减的字段
     * @param float|int $dec 减的值
     * @param string $keyField id的字段
     * @param bool $minus 是否可以为负数
     * @param int $acc 精度
     * @return bool
     */
    public function bcDec($key, string $decField, string $dec, string $keyField = null, int $acc = 2)
    {
        return $this->bc($key, $decField, $dec, $keyField, 2);
    }

    /**
     * 高精度计算并保存
     * @param $key
     * @param string $incField
     * @param string $inc
     * @param string|null $keyField
     * @param int $type
     * @param int $acc
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function bc($key, string $incField, string $inc, string $keyField = null, int $type = 1, int $acc = 2)
    {
        if ($keyField === null) {
            $result = $this->get($key);
        } else {
            $result = $this->getOne([$keyField => $key]);
        }
        if (!$result) return false;
        if ($type === 1) {
            $new = bcadd($result[$incField], $inc, $acc);
        } else if ($type === 2) {
            if ($result[$incField] < $inc) return false;
            $new = bcsub($result[$incField], $inc, $acc);
        }
        $result->{$incField} = $new;
        return false !== $result->save();
    }

}
