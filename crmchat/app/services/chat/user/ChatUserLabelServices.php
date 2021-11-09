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


use app\dao\chat\user\ChatUserLabelDao;
use crmeb\basic\BaseServices;
use crmeb\services\FormBuilder as Form;
use think\exception\ValidateException;

/**
 * Class ChatUserLabelServices
 * @package app\services\chat\user
 */
class ChatUserLabelServices extends BaseServices
{

    /**
     * ChatUserLabelServices constructor.
     * @param ChatUserLabelDao $dao
     */
    public function __construct(ChatUserLabelDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 标签列表
     * @param int $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserLabel(int $userId)
    {
        return $this->dao->getDataList(['user_id' => $userId], ['*'], 'id', 0, 0, [
            'user' => function ($query) {
                $query->field(['nickname', 'id', 'avatar']);
            }
        ]);
    }

    /**
     * 获取列表
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabeList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list  = $this->dao->getDataList($where, ['*'], 'id', $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 表单规则
     * @param array $label
     * @return array
     */
    public function fromRule(array $label = [])
    {
        /** @var ChatUserLabelCateServices $service */
        $service = app()->make(ChatUserLabelCateServices::class);
        $options = $service->getColumn(['type' => 0], 'id as value,name as label');
        return [
            Form::select('cate_id', '标签分类', $label['cate_id'] ?? 0)->options($options),
            Form::input('label', '标签名称', $label['label'] ?? ''),
        ];
    }

    /**
     * 获取创建标签表单
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function getFormCreate()
    {
        return create_form('创建标签', $this->fromRule(), $this->url('user/label'));
    }

    /**
     * 获取修改标签表单
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFormEdit(int $id)
    {
        $label = $this->dao->get($id);
        if (!$label) {
            throw new ValidateException('修改的标签不存在');
        }
        return create_form('修改标签', $this->fromRule($label->toArray()), $this->url('user/label/' . $id), 'put');
    }
}
