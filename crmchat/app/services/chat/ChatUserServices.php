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

namespace app\services\chat;


use app\dao\chat\ChatUserDao;
use app\services\chat\user\ChatUserGroupServices;
use app\services\chat\user\ChatUserLabelAssistServices;
use Carbon\Carbon;
use crmeb\basic\BaseServices;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use crmeb\services\FormBuilder as Form;

/**
 * Class ChatUserServices
 * @package app\services\chat
 * @method getUserList(array $where, string $field = '*') 获取用户列表
 * @method max(array $where)
 */
class ChatUserServices extends BaseServices
{

    /**
     * ChatUserServices constructor.
     * @param ChatUserDao $dao
     */
    public function __construct(ChatUserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 统计客户人数
     * @param int $id
     * @return array
     */
    public function getKefuSum(string $appid = '')
    {
        $all = $this->dao->count(['appid' => $appid]);
        $toDayKefu = $this->dao->count(['time' => 'today', 'appid' => $appid, 'is_tourist' => 0]);
        $month = $this->dao->count(['time' => 'month', 'appid' => $appid]);
        $toDayTourist = $this->dao->count(['time' => 'today', 'appid' => $appid, 'is_tourist' => 1]);
        return compact('all', 'toDayKefu', 'month', 'toDayTourist');
    }

    /**
     * 获取用户数据
     * @param int $userId
     * @param string[] $field
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserInfo(int $userId, array $field = ['*'], array $with = [])
    {
        return $this->dao->get($userId, $field, $with);
    }

    /**
     * @param App $app
     * @return $this
     */
    public function setApp(App $app)
    {
        $this->dao->setApp($app);
        return $this;
    }

    /**
     * 获取客服用户
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChatUserList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getUserModel($where)->with(['groupOne', 'label'])->page($page, $limit)->order('id', 'desc')->field(['*'])->select()->toArray();
        $count = $this->dao->getUserModel($where)->count();
        return compact('list', 'count');
    }

    /**
     * 获取修改表单
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChatUserForm(int $id)
    {
        $userInfo = $this->dao->get($id);
        if (!$userInfo) {
            throw new ValidateException('修改的用户不存在');
        }
        /** @var ChatUserGroupServices $groupService */
        $groupService = app()->make(ChatUserGroupServices::class);
        $option = $groupService->getColumn([], 'group_name as label,id as value');
        $rule = [
            Form::frameImage('avatar', '用户头像', $this->url('admin/widget.images/index.html', ['fodder' => 'avatar', 'big' => 1]), $userInfo->getAttr('avatar'))->icon('ios-image')->width('950px')->height('420px'),
            Form::input('nickname', '用户昵称', $userInfo->getAttr('nickname')),
            Form::input('remark_nickname', '备注昵称', $userInfo->getAttr('remark_nickname')),
            Form::input('phone', '手机号', $userInfo->getAttr('phone')),
            Form::select('group_id', '用户分组', $userInfo->getAttr('group_id'))->options($option),
            Form::textarea('remarks', '用户备注', $userInfo->getAttr('remarks')),
        ];
        return create_form('修改用户', $rule, $this->url('user/update/' . $id), 'put');
    }

    /**
     * 更新用户分类
     * @param array $ids
     * @param int $groupId
     * @return \crmeb\basic\BaseModel|mixed|\think\Model
     */
    public function batchUpdateGroup(array $ids, int $groupId)
    {
        return $this->dao->searchModel()->where('id', 'in', $ids)->update(['group_id' => $groupId]);
    }

    /**
     * @param array $ids
     * @param array $labelId
     * @param array $unLabelId
     * @return mixed
     */
    public function batchUpdateLabel(array $ids, array $labelId, array $unLabelId)
    {
        /** @var ChatUserLabelAssistServices $service */
        $service = app()->make(ChatUserLabelAssistServices::class);
        return $service->updateLabel($ids, $labelId, $unLabelId);
    }

    /**
     * @param string $time
     * @return array
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function getKefuMobileStatistics(string $time)
    {
        $res = [
            'tourist' => $this->dao->getKefuMobileStatisticsList($time, 1),
            'list' => $this->dao->getKefuMobileStatisticsList($time, 0),
        ];

        foreach ($res['tourist'] as &$item) {
            if (strstr($item['time'], '-') !== false) {
                $item['time'] = explode('-', $item['time']);
                $item['time'] = $item['time'][count($item['time']) - 1];
            }
        }
        foreach ($res['list'] as &$item) {
            if (strstr($item['time'], '-') !== false) {
                $item['time'] = explode('-', $item['time']);
                $item['time'] = $item['time'][count($item['time']) - 1];
            }
        }

        return $res;
    }

    /**
     * 获取统计数据
     * @param int $id
     * @param int $year
     * @param int $month
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getKefuStatistics(int $id, int $type, int $year, int $month)
    {
        if ($type) {
            $date = Carbon::create($year, $month);
            $startTime = $date->startOfMonth()->toDateTimeString();
            $endTime = $date->endOfMonth()->toDateTimeString();
        } else {
            $date = Carbon::create($year);
            $startTime = $date->startOfYear()->toDateTimeString();
            $endTime = $date->endOfYear()->toDateTimeString();
        }


        return [
            'list' => $this->dao->kefuStatistics([
                'user_id' => $id,
                'type' => $type,
                'is_tourist' => 0,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ]),
            'tourist' => $this->dao->kefuStatistics([
                'user_id' => $id,
                'type' => $type,
                'is_tourist' => 1,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ])
        ];
    }

}
