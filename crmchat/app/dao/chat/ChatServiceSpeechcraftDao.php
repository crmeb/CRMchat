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


use app\models\chat\ChatServiceSpeechcraft;
use crmeb\basic\BaseDao;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 话术
 * Class ChatServiceSpeechcraftDao
 * @package app\dao\chat
 */
class ChatServiceSpeechcraftDao extends BaseDao
{

    /**
     * 获取当前模型
     * @return string
     */
    protected function setModel(): string
    {
        return ChatServiceSpeechcraft::class;
    }

    /**
     * 获取话术列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSpeechcraftList(array $where, int $page, int $limit)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('sort DESC')->select()->toArray();
    }
}
