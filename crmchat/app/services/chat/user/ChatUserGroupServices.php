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


use app\dao\chat\user\ChatUserGroupDao;
use app\services\chat\ChatUserServices;
use crmeb\basic\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\exception\ValidateException;
use think\facade\Route as Url;
use think\Model;

/**
 * Class ChatUserGroupServices
 * @package app\services\chat\user
 */
class ChatUserGroupServices extends BaseServices
{

    /**
     * ChatUserGroupServices constructor.
     * @param ChatUserGroupDao $dao
     */
    public function __construct(ChatUserGroupDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取某一个分组
     * @param int $id
     * @return array|Model|null
     */
    public function getGroup(int $id)
    {
        return $this->dao->get($id);
    }

    /**
     * 获取分组列表
     * @param string $feild
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupList($feild = ['id', 'group_name'], bool $is_page = false)
    {
        $page = $limit = 0;
        if ($is_page) {
            [$page, $limit] = $this->getPageValue();
            $count = $this->dao->count([]);
        }
        $list = $this->dao->getDataList([], $feild, 'id', $page, $limit);

        return $is_page ? compact('list', 'count') : $list;
    }

    /**
     * 获取一些用户的分组名称
     * @param array $ids
     */
    public function getUsersGroupName(array $ids)
    {
        return $this->dao->getColumn([['id', 'IN', $ids]], 'group_name', 'id');
    }

    /**
     * 添加/修改分组页面
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function add(int $id = 0)
    {
        $group = $id ? $this->getGroup($id) : null;
        $field = array();
        if (!$group) {
            $title   = '添加分组';
            $field[] = Form::input('group_name', '分组名称', '')->required();
        } else {
            $title   = '修改分组';
            $field[] = Form::hidden('id', $id);
            $field[] = Form::input('group_name', '分组名称', $group->getData('group_name'))->required();
        }
        return create_form($title, $field, $id ? Url::buildUrl('/user/group/' . $id) : Url::buildUrl('/user/group'), $id ? 'PUT' : 'POST');
    }

    /**
     * 添加|修改
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function save(int $id, array $data)
    {
        $groupName = $this->dao->getOne(['group_name' => $data['group_name']]);
        if ($id) {
            if (!$this->getGroup($id)) {
                throw new AdminException('数据不存在');
            }
            if ($groupName && $id != $groupName['id']) {
                throw new AdminException('该分组已经存在');
            }
            if ($this->dao->update($id, $data)) {
                return true;
            } else {
                throw new AdminException('修改失败或者您没有修改什么！');
            }
        } else {
            unset($data['id']);
            if ($groupName) {
                throw new AdminException('该分组已经存在');
            }
            if ($this->dao->save($data)) {
                return true;
            } else {
                throw new AdminException('添加失败！');
            }
        }
    }

    /**
     * 删除
     * @param int $id
     */
    public function delGroup(int $id)
    {
        /** @var ChatUserServices $userServices */
        $userServices = app()->make(ChatUserServices::class);
        if ($userServices->count(['group_id' => $id])) {
            throw new AdminException('请先清除掉,关联的用户分组');
        }
        if (!$this->dao->delete($id)) {
            throw new AdminException('删除失败,请稍候再试!');
        }

    }
}
