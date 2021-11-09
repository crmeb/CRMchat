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


use app\dao\chat\ChatServiceFeedbackDao;
use crmeb\basic\BaseServices;
use crmeb\services\FormBuilder;
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;

/**
 * 话术
 * Class ChatServiceFeedbackServices
 * @package app\services\chat
 */
class ChatServiceFeedbackServices extends BaseServices
{

    /**
     * ChatServiceFeedbackServices constructor.
     * @param ChatServiceFeedbackDao $dao
     */
    public function __construct(ChatServiceFeedbackDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取反馈列表
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getFeedbackList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $data  = $this->dao->getFeedback($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('data', 'count');
    }

    /**
     * 获取修改表单
     * @param int $id
     * @return array
     * @throws FormBuilderException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function editForm(int $id)
    {
        $feedInfo = $this->dao->get($id);
        if (!$feedInfo) {
            throw new ValidateException('反馈内容没有查到');
        }
        $feedInfo = $feedInfo->toArray();
        $field    = [
            FormBuilder::textarea('make', '备注', $feedInfo['make'] ?? '')->col(22),
        ];
        if (!$feedInfo['status']) {
            $field[] = FormBuilder::radio('status', '状态', 0)->setOptions([
                ['label' => '已处理', 'value' => 1],
                ['label' => '未处理', 'value' => 0]
            ]);
        }
        return create_form($feedInfo['status'] ? '备注' : '处理', $field, $this->url('/chat/feedback/' . $id), 'PUT');
    }

}
