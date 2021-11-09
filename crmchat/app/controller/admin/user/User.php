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

namespace app\controller\admin\user;


use app\controller\admin\AuthController;
use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatUserServices;
use app\services\chat\user\ChatUserGroupServices;
use app\services\chat\user\ChatUserLabelCateServices;
use app\services\chat\user\ChatUserLabelServices;

/**
 * Class User
 * @package app\controller\admin\chat
 */
class User extends AuthController
{

    /**
     * User constructor.
     * @param ChatUserServices $services
     */
    public function __construct(ChatUserServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['nickname', ''],
            ['group_id', ''],
            ['label_id', ''],
            ['time', ''],
            ['sex', ''],
            ['user_type', ''],
            ['field_key', ''],
            ['is_tourist', '']
        ]);

        return $this->success($this->services->getChatUserList($where));
    }

    /**
     * @param ChatUserLabelCateServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLavelAll(ChatUserLabelCateServices $services)
    {
        $list = $services->getLabelAll(0);
        foreach ($list as &$item) {
            $item['options'] = $item['label'];
            foreach ($item['options'] as &$value) {
                $value['value'] = $value['id'];
            }
            $item['label'] = $item['name'];
            $item['value'] = $item['id'];
            unset($item['name'], $item['id']);
        }
        return $this->success($list);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if (!$id) {
            return $this->fail('缺少参数');
        }
        return $this->success($this->services->getChatUserForm((int)$id));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function update(ChatServiceRecordServices $services, $id)
    {
        $data = $this->request->postMore([
            ['avatar', ''],
            ['nickname', ''],
            ['group_id', 0],
            ['remarks', ''],
            ['remark_nickname', ''],
            ['phone', ''],
        ]);
        if (!$data['avatar']) {
            return $this->fail('用户头像必须填写');
        }
        if (!$data['nickname']) {
            return $this->fail('用户昵称必须填写');
        }

        $userInfo = $this->services->get(['id' => $id], ['avatar', 'nickname']);

        $this->services->update($id, $data);

        $update = [];
        if ($data['avatar'] != $userInfo->avatar) {
            $update['avatar'] = $data['avatar'];
        }
        if ($data['nickname'] != $userInfo->nickname) {
            $update['nickname'] = $data['nickname'];
        }
        if ($update) {
            $services->update(['to_user_id' => $id], $update);
        }

        return $this->success('修改成功');
    }

    /**
     * 批量
     * @return mixed
     */
    public function batchGroup()
    {
        [$ids, $groupId] = $this->request->postMore([
            ['ids', []],
            ['group_id', 0]
        ], true);
        if (!$ids) {
            return $this->fail('至少选择一个用户');
        }
        if (!$groupId) {
            return $this->fail('请选择分组');
        }
        $this->services->batchUpdateGroup($ids, (int)$groupId);
        return $this->success('批量设置成功');
    }

    /**
     * 设置标签
     * @return mixed
     */
    public function batchLabel()
    {
        [$ids, $labelId, $unLabelId] = $this->request->postMore([
            ['ids', []],
            ['label_id', []],
            ['un_label_id', []]
        ], true);
        if (!$ids) {
            return $this->fail('至少选择一个用户');
        }
        if (!$labelId && !$unLabelId) {
            return $this->fail('至少设置或者取消设置一个标签');
        }

        $this->services->batchUpdateLabel($ids, $labelId, $unLabelId);

        return $this->success('设置成功');
    }

    /**
     * 获取所有标签
     * @param ChatUserLabelCateServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelAll(ChatUserLabelCateServices $services)
    {
        $id = $this->request->get('id', 0);
        return $this->success($services->getLabelAll((int)$id));
    }

    /**
     * 获取所有分组
     * @param ChatUserGroupServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupAll(ChatUserGroupServices $services)
    {
        return $this->success($services->getGroupList());
    }

}
