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


use app\dao\chat\ChatAutoReplyDao;
use crmeb\basic\BaseServices;
use crmeb\services\FormBuilder;

/**
 * Class ChatAutoReplyServices
 * @package app\services\chat
 * @method array getReplyList(array $where) 获取回复列表
 * @method ChatAutoReplyDao setApp($app) 设置app
 */
class ChatAutoReplyServices extends BaseServices
{

    /**
     * ChatAutoReplyServices constructor.
     * @param ChatAutoReplyDao $dao
     */
    public function __construct(ChatAutoReplyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param string $appId
     * @param int $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAuthReply(string $appId, int $userId)
    {
        [$page, $limit] = $this->getPageValue();

        return $this->dao->getReply(['appid' => $appId, 'user_id' => $userId], $page, $limit);
    }

    /**
     * 获取列表
     * @param string $appId
     * @param int $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(string $appId, int $userId)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getReply(['appid' => $appId, 'user_id' => $userId], $page, $limit);
        $count = $this->dao->count(['appid' => $appId, 'user_id' => $userId]);
        return compact('list', 'count');
    }

    /**
     * 获取表单
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getForm(int $id, int $userId, string $appId)
    {
        $data = [];
        if ($id) {
            $data = $this->dao->get($id);
            if ($data) {
                $data = $data->toArray();
            }
        }
        $field = [
            FormBuilder::input('keyword', '关键字', $data['keyword'] ?? '')->required()->placeholder('请输入关键字,多个关键字用逗号隔开'),
            FormBuilder::textarea('content', '回复内容', $data['content'] ?? '')->required()->placeholder('请输入回复内容'),
            FormBuilder::number('sort', '排序', $data['sort'] ?? 0),
            FormBuilder::hidden('user_id', $userId),
            FormBuilder::hidden('appid', $appId),
        ];

        return create_form($id ? '修改自动回复' : '添加自动回复', $field, '/chat/reply/' . $id, 'post');
    }
}
