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

namespace app\dao\system\log;


use crmeb\basic\BaseDao;
use app\models\system\log\SystemLog;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 系统日志
 * Class SystemLogDao
 * @package app\dao\system\log
 */
class SystemLogDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return SystemLog::class;
    }

    /**
     * 删除过期日志
     * @throws \Exception
     */
    public function deleteLog()
    {
        $this->getModel()->where('add_time', '<', time() - 7776000)->delete();
    }

    /**
     * 获取系统日志列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getLogList(array $where, int $page, int $limit)
    {
        return $this->search($where)->page($page, $limit)->order('add_time DESC')->select()->toArray();
    }

}
