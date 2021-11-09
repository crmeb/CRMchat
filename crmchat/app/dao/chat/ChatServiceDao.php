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


use app\models\chat\ChatService;
use crmeb\basic\BaseDao;
use crmeb\basic\BaseModel;

/**
 * 客服dao
 * Class ChatServiceDao
 * @package app\dao\chat
 */
class ChatServiceDao extends BaseDao
{

    /**
     * @return string
     */
    protected function setModel(): string
    {
        return ChatService::class;
    }

    /**
     * 不存在的用户直接禁止掉
     * @param array $uids
     * @return bool|BaseModel
     */
    public function deleteNonExistentService(array $uids = [])
    {
        if ($uids) {
            return $this->getModel()->whereIn('user_id', $uids)->update(['status' => 0]);
        } else {
            return true;
        }
    }

    /**
     * 获取客服列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceList(array $where, int $page, int $limit)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->when(isset($where['noId']), function ($query) use ($where) {
            $query->whereNotIn('user_id', $where['noId']);
        })->order('id DESC')->field('appid,online,account,id,user_id,avatar,nickname,status,add_time,phone')->select()->toArray();
    }

    /**
     * 获取接受通知的客服
     * @return array
     */
    public function getStoreServiceOrderNotice(int $customer = 0)
    {
        return $this->getModel()->where(['status' => 1, 'notify' => 1])->when($customer, function ($query) use ($customer) {
            $query->where('customer', $customer);
        })->field(['nickname', 'phone', 'user_id', 'customer'])->select()->toArray();
    }

    /**
     *
     * @param array $where
     * @param array $data
     * @return BaseModel
     */
    public function updateOnline(array $where, array $data)
    {
        return $this->getModel()->whereNotIn('user_id', $where['notUid'])->update($data);
    }
}
