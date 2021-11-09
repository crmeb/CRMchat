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

namespace app\services\chat\user;


use app\dao\chat\user\ChatUserLabelAssistDao;
use crmeb\basic\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Class ChatUserLabelAssistServices
 * @package app\services\chat\user
 */
class ChatUserLabelAssistServices extends BaseServices
{

    /**
     * ChatUserLabelAssistServices constructor.
     * @param ChatUserLabelAssistDao $dao
     */
    public function __construct(ChatUserLabelAssistDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 设置用户标签
     * @param int $userId
     * @param array $userIds
     * @param string $label
     */
    public function setUserLable($uids, array $labels)
    {
        if (!count($labels)) {
            return true;
        }
        if (!is_array($uids)) {
            $uids = [$uids];
        }
        $re = $this->dao->delete([
            ['user_id', 'in', $uids],
            ['label_id', 'in', $labels],
        ]);
        if ($re === false) {
            throw new AdminException('清空用户标签失败');
        }
        $data = [];
        foreach ($uids as $uid) {
            foreach ($labels as $label) {
                $data[] = ['user_id' => $uid, 'label_id' => $label];
            }
        }
        if ($data) {
            if (!$this->dao->saveAll($data))
                throw new AdminException('设置标签失败');
        }
        return true;
    }

    /**
     * 取消用户标签
     * @param int $uid
     * @param array $labels
     * @return mixed
     */
    public function unUserLabel(int $userId, array $labels)
    {
        if (!count($labels)) {
            return true;
        }
        $this->dao->delete([
            ['user_id', '=', $userId],
            ['label_id', 'in', $labels],
        ]);
        return true;
    }

    /**
     * 更新
     * @param array $ids
     * @param array $labelId
     * @param array $unLabelId
     * @return mixed
     */
    public function updateLabel(array $ids, array $labelId, array $unLabelId)
    {
        $data = [];
        foreach ($ids as $k) {
            foreach ($unLabelId as $id) {
                $this->dao->delete(['user_id' => $k, 'label_id' => $id]);
            }
            foreach ($labelId as $label) {
                $data[] = [
                    'user_id'  => $k,
                    'label_id' => $label
                ];
            }
        }
        return $this->dao->saveAll($data);
    }
}
