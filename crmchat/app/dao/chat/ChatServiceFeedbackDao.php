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

namespace app\dao\chat;


use app\models\chat\ChatServiceFeedback;
use crmeb\basic\BaseDao;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class ChatServiceFeedbackDao
 * @package app\dao\chat
 */
class ChatServiceFeedbackDao extends BaseDao
{

    /**
     * 获取当前模型
     * @return string
     */
    protected function setModel(): string
    {
        return ChatServiceFeedback::class;
    }

    /**
     * 获取用户反馈信息列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getFeedback(array $where, int $page, int $limit)
    {
        return $this->search($where)->page($page, $limit)->order('id DESC')->select()->toArray();
    }
}
