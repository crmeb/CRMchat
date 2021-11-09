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


use app\dao\chat\ChatServiceSpeechcraftDao;
use crmeb\basic\BaseServices;
use crmeb\services\FormBuilder;
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;

/**
 * 话术
 * Class ChatServiceSpeechcraftServices
 * @package app\services\chat
 */
class ChatServiceSpeechcraftServices extends BaseServices
{

    /**
     * ChatServiceSpeechcraftServices constructor.
     * @param ChatServiceSpeechcraftDao $dao
     */
    public function __construct(ChatServiceSpeechcraftDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSpeechcraftList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getSpeechcraftList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 创建form表单
     * @return mixed
     */
    public function createForm()
    {
        return create_form('添加话术', $this->speechcraftForm(), $this->url('chat/speechcraft'), 'POST');
    }

    /**
     * @param int $id
     * @return array
     * @throws FormBuilderException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function updateForm(int $id)
    {
        $info = $this->dao->get($id);
        if (!$info) {
            throw new ValidateException('您修改的话术内容不存在');
        }
        return create_form('编辑话术', $this->speechcraftForm($info->toArray()), $this->url('chat/speechcraft/' . $id), 'PUT');
    }

    /**
     * @param array $infoData
     * @return mixed
     */
    protected function speechcraftForm(array $infoData = [])
    {
        /** @var ChatServiceSpeechcraftCateServices $services */
        $services = app()->make(ChatServiceSpeechcraftCateServices::class);
        $cateList = $services->getCateList(['owner_id' => 0, 'type' => 1]);
        $data = [];
        foreach ($cateList['data'] as $item) {
            $data[] = ['value' => $item['id'], 'label' => $item['name']];
        }
        $form[] = FormBuilder::select('cate_id', '话术分类', $infoData['cate_id'] ?? '')->setOptions($data);
        $form[] = FormBuilder::textarea('title', '话术标题', $infoData['title'] ?? '');
        $form[] = FormBuilder::textarea('message', '话术内容', $infoData['message'] ?? '')->required();
        $form[] = FormBuilder::number('sort', '排序', (int)($infoData['sort'] ?? 0));
        return $form;
    }
}
