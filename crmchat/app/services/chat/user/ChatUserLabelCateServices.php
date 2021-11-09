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


use app\services\other\CategoryServices;
use crmeb\basic\BaseDao;
use crmeb\services\FormBuilder as Form;
use think\exception\ValidateException;

/**
 * 标签分类
 * Class ChatUserLabelCateServices
 * @package app\services\chat\user
 * @property BaseDao $dao
 */
class ChatUserLabelCateServices extends CategoryServices
{

    /**
     * 规则
     * @param array $cateInfo
     * @return array
     */
    public function formRule(array $cateInfo = [])
    {
        return [
            Form::input('name', '分类名称', $cateInfo['name'] ?? ''),
        ];
    }

    /**
     * 创建
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function getCreateForm()
    {
        return create_form('添加标签分类', $this->formRule(), $this->url('user/label/cate'));
    }

    /**
     * 修改分类
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function getEditForm(int $id)
    {
        $cateInfo = $this->dao->get($id);
        if (!$cateInfo) {
            throw new ValidateException('获取分类失败');
        }
        return create_form('添加标签分类', $this->formRule($cateInfo->toArray()), $this->url('user/label/cate/' . $id), 'put');
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelAll(int $id)
    {
        $labelAll = $this->dao->getDataList(['type' => 0], ['name', 'id'], 'id', 0, 0, ['label' => function ($query) {
            $query->with(['userone' => function ($query) {
                $query->field(['count(*) count_user', 'label_id', 'user_id']);
            }]);
        }]);
        if ($id) {
            /** @var ChatUserLabelAssistServices $service */
            $service = app()->make(ChatUserLabelAssistServices::class);
            $labelIds = $service->getColumn(['user_id' => $id], 'label_id');
            foreach ($labelAll as &$item) {
                if ($item['label']) {
                    foreach ($item['label'] as &$label) {
                        if (in_array($label['id'], $labelIds)) {
                            $label['disabled'] = true;
                        } else {
                            $label['disabled'] = false;
                        }
                    }
                }
            }
        }
        return $labelAll;
    }
}
