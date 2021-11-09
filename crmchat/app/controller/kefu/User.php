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

namespace app\controller\kefu;


use app\jobs\AutoBadge;
use app\Request;
use app\services\chat\ChatComplainServices;
use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceFeedbackServices;
use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatServiceServices;
use app\services\chat\ChatUserServices;
use app\services\chat\user\ChatUserGroupServices;
use app\services\chat\user\ChatUserLabelAssistServices;
use app\services\chat\user\ChatUserLabelCateServices;
use app\services\chat\user\ChatUserLabelServices;
use app\services\other\CacheServices;
use app\services\other\CategoryServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\validate\chat\ChatServiceFeedbackValidate;
use app\validate\chat\ChatServiceValidate;
use crmeb\services\CacheService;
use crmeb\services\UploadService;
use crmeb\utils\Character;
use Psr\SimpleCache\InvalidArgumentException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Config;

/**
 * Class User
 * @package app\controller\kefu
 */
class User extends AuthController
{

    /**
     * User constructor.
     * @param ChatServiceRecordServices $services
     */
    public function __construct(ChatServiceRecordServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 获取当前登录客服
     * @param ChatServiceServices $services
     * @return mixed
     */
    public function getKefuInfo(ChatServiceServices $services)
    {
        $kefuInfo = $this->kefuInfo->toArray();
        $kefuInfo['password'] = '******';
        $kefuInfo['site_title'] = sys_config('site_name');
        $kefuInfo['user_ids'] = $services->getColumn(['appid' => $kefuInfo['appid']], 'user_id');
        return $this->success($kefuInfo);
    }

    /**
     * 个人中心修改客服信息
     * @param ChatServiceValidate $validate
     * @param ChatServiceServices $services
     * @param ChatUserServices $userServices
     * @return mixed
     */
    public function updateKefu(ChatServiceValidate $validate, ChatServiceServices $services, ChatUserServices $userServices)
    {
        $data = $this->request->postMore([
            ['nickname', ''],
            ['avatar', ''],
            ['password', ''],
            ['phone', ''],
        ]);

        $validate->check($data);

        if ($data['password'] === '******') {
            unset($data['password']);
        } else if ($data['password'] !== '******' && $data['password']) {
            $data['password'] = $services->passwordHash($data['password']);
        }
        if (!$data['password']) {
            unset($data['password']);
        }

        $services->update($this->kefuId, $data);
        $update = [];
        if ($data['avatar']) {
            $update['avatar'] = $data['avatar'];
        }
        if ($data['nickname']) {
            $update['nickname'] = $data['nickname'];
        }
        if ($update) {
            $this->services->update(['to_user_id' => $this->kefuId], $update);
            $userServices->update(['id' => $this->kefuInfo['user_id']], $update);
        }

        return $this->success('修改成功');
    }

    /**
     * 获取客户列表
     * @param Character $character
     * @param string $nickname
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getUserList(Character $character, string $nickname = '')
    {
        $labelId = $this->request->get('label_id', '');
        return $this->success(
            $character->groupByInitials(
                $this->services->getUserList(
                    $this->kefuInfo['appid'],
                    $this->kefuInfo['user_id'],
                    $nickname, $labelId),
                'nickname'
            )
        );
    }

    /**
     * 获取当前客服和用户的聊天记录
     * @param string $nickname
     * @param string $is_tourist
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function recordList(string $nickname = '', $is_tourist = '')
    {
        return $this->success($this->services->getServiceList($this->kefuInfo['appid'], (int)$this->kefuInfo['user_id'], $nickname, $is_tourist));
    }

    /**
     * 获取用户信息
     * @param ChatUserServices $services
     * @param $userId
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function userInfo(ChatUserServices $services, $userId)
    {
        $userInfo = $services->getUserInfo((int)$userId, ['*'], ['label']);
        if (!$userInfo) {
            return $this->fail('用户不存在');
        }
        $userInfo = $userInfo->toArray();
        //兼容前端取值错误
        $userInfo['to_user_id'] = $userInfo['id'];
        return $this->success($userInfo);
    }

    /**
     * 用户标签
     * @param ChatUserLabelCateServices $services
     * @return mixed
     */
    public function getUserLabel(ChatUserLabelCateServices $services)
    {
        $id = $this->request->get('id', 0);
        return $this->success($services->getLabelAll((int)$id));
    }

    /**
     * 获取所有用户标签下面的用户
     * @param ChatUserLabelServices $services
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getLabelAll(ChatUserLabelServices $services)
    {
        return $this->success($services->getUserLabel(0));
    }

    /**
     * 获取用户分组
     * @param ChatUserGroupServices $services
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getUserGroup(ChatUserGroupServices $services)
    {
        return $this->success($services->getGroupList());
    }

    /**
     * 设置分组
     * @param ChatUserGroupServices $services
     * @param ChatUserServices $userServices
     * @param $userId
     * @param $id
     * @return mixed
     */
    public function setUserGroup(ChatUserGroupServices $services, ChatUserServices $userServices, $userId, $id)
    {
        if (!$services->count(['id' => $id])) {
            return $this->fail('添加的会员标签不存在');
        }
        if (!($userInfo = $userServices->get($userId))) {
            return $this->fail('用户不存在');
        }
        if ($userInfo->group_id == $id) {
            return $this->fail('已拥有此分组');
        }
        $userInfo->group_id = $id;
        if ($userInfo->save()) {
            return $this->success('设置成功');
        } else {
            return $this->fail('设置失败');
        }
    }

    /**
     * 设置用户标签
     * @param ChatUserLabelAssistServices $services
     * @param $userId
     * @return mixed
     */
    public function setUserLabel(ChatUserLabelAssistServices $services, $userId)
    {
        [$labels, $unLabelIds] = $this->request->postMore([
            ['label_ids', []],
            ['un_label_ids', []]
        ], true);
        if (!count($labels) && !count($unLabelIds)) {
            return $this->fail('缺少标签id');
        }
        if ($services->setUserLable($userId, $labels) && $services->unUserLabel($userId, $unLabelIds)) {
            return $this->success('设置成功');
        } else {
            return $this->fail('设置失败');
        }
    }

    /**
     * 删除单个标签下的人
     * @param ChatUserLabelAssistServices $services
     * @param $userId
     * @param $labelId
     * @return mixed
     */
    public function delUserLabel(ChatUserLabelAssistServices $services, $userId, $labelId)
    {
        if (!$labelId || !$userId) {
            return $this->fail('缺少参数');
        }
        if ($services->delete(['user_id' => $userId, 'label_id' => $labelId])) {
            return $this->success('删除成功');
        } else {
            return $this->fail('删除失败');
        }
    }

    /**
     * 退出登陆
     * @param ChatServiceServices $services
     * @param ChatUserServices $userServices
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function logout(ChatServiceServices $services, ChatUserServices $userServices)
    {
        $key = trim(ltrim($this->request->header(Config::get('cookie.token_name')), 'Bearer'));
        CacheService::redisHandler()->delete($key);
        $services->update(['user_id' => $this->kefuInfo['user_id'], 'appid' => $this->kefuInfo['appid']], ['online' => 0, 'client_id' => '', 'is_app' => 0, 'is_backstage' => 0]);
        $userServices->update(['id' => $this->kefuInfo['user_id']], ['online' => 0]);
        return $this->success();
    }

    /**
     * 图片上传
     * @param Request $request
     * @param SystemAttachmentServices $services
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function upload(Request $request, SystemAttachmentServices $services)
    {
        $data = $request->postMore([
            ['filename', 'file'],
        ]);
        if (!$data['filename']) return $this->fail('参数有误');
        if (CacheService::has('start_uploads_' . $request->kefuId()) && CacheService::get('start_uploads_' . $request->kefuId()) >= 100) return $this->fail('非法操作');
        $upload = UploadService::init();
        $info = $upload->to('store/comment')->validate()->move($data['filename']);
        if ($info === false) {
            return $this->fail($upload->getError());
        }
        $res = $upload->getUploadInfo();
        $services->attachmentAdd($res['name'], $res['size'], $res['type'], $res['dir'], $res['thumb_path'], 1, (int)sys_config('upload_type', 1), $res['time'], 2);
        if (CacheService::has('start_uploads_' . $request->kefuId()))
            $start_uploads = (int)CacheService::get('start_uploads_' . $request->kefuId());
        else
            $start_uploads = 0;
        $start_uploads++;
        CacheService::set('start_uploads_' . $request->kefuId(), $start_uploads, 86400);
        $res['dir'] = path_to_url($res['dir']);
        if (strpos($res['dir'], 'http') === false) $res['dir'] = $request->domain() . $res['dir'];
        return $this->success('图片上传成功!', ['name' => $res['name'], 'url' => $res['dir']]);
    }

    /**
     * 获取当前客服所有没读条数
     * @param ChatServiceRecordServices $services
     * @return mixed
     */
    public function getMessageCount(ChatServiceRecordServices $services)
    {
        AutoBadge::dispatch([$this->kefuInfo['user_id'], 0, $this->kefuInfo['appid']]);
        return $this->success(['count' => $services->sum(['appid' => $this->kefuInfo['appid'], 'user_id' => $this->kefuInfo['user_id']], 'mssage_num')]);
    }

    /**
     * 保存client_id
     * @param ChatServiceServices $services
     * @return mixed
     */
    public function updateService(ChatServiceServices $services)
    {
        $clientId = $this->request->post('client_id', '');
        if ($clientId) {
            $services->update(['client_id' => $clientId], ['client_id' => '']);
            $services->update($this->kefuId, ['client_id' => $clientId]);
            AutoBadge::dispatch([$this->kefuInfo['user_id'], 0, $this->kefuInfo['appid']]);
        }
        return $this->success();
    }

    /**
     * 修改用户信息
     * @param ChatUserServices $services
     * @param ChatServiceRecordServices $recordServices
     * @param $userId
     * @return mixed
     */
    public function updateUser(ChatUserServices $services, ChatServiceRecordServices $recordServices, $userId)
    {
        $data = $this->request->postMore([
            ['nickname', ''],
            ['remark_nickname', ''],
            ['sex', ''],
            ['phone', ''],
            ['remarks', ''],
        ]);
        $update = [];
        if ($data['phone'] && !preg_match('/^(13[0-9]|14[01456879]|15[0-35-9]|16[2567]|17[0-8]|18[0-9]|19[0-35-9])\d{8}$/', $data['phone'])) {
            return $this->fail('请输入正确的手机号');
        }
        foreach ($data as $key => $val) {
            if ($val) {
                $update[$key] = $val;
            }
        }
        if ($update) {

            if (isset($data['phone']) && $data['phone']) {
                $update['is_tourist'] = 0;
                $recordServices->update(['to_user_id' => $userId], ['is_tourist' => 0]);
            }

            $services->update($userId, $update);
        }
        return $this->success('修改成功');
    }

    /**
     * 保存反馈信息
     * @param Request $request
     * @param ChatServiceFeedbackServices $services
     * @return mixed
     */
    public function saveFeedback(Request $request, ChatServiceFeedbackServices $services)
    {
        $data = $request->postMore([
            ['rela_name', ''],
            ['phone', ''],
            ['content', ''],
        ]);

        validate(ChatServiceFeedbackValidate::class)->check($data);

        $data['content'] = htmlspecialchars($data['content']);
        $data['add_time'] = time();
        $data['uid'] = $this->kefuInfo['user_id'];
        $services->save($data);
        return $this->success('保存成功');
    }

    /**
     *
     * @param ChatUserServices $services
     * @param ChatServiceDialogueRecordServices $dialogueRecordServices
     * @param $userId
     * @return mixed
     */
    public function status(ChatUserServices $services, ChatServiceDialogueRecordServices $dialogueRecordServices, $userId)
    {
        if (!$userId) {
            return $this->fail('缺少用户id');
        }

        $this->services->transaction(function () use ($userId, $dialogueRecordServices, $services) {
            $this->services->delete(['to_user_id' => $userId]);
            $this->services->delete(['user_id' => $userId]);
            $dialogueRecordServices->delete(['to_user_id' => $userId]);
            $services->delete($userId);
        });

        return $this->success('拉黑成功');
    }

    /**
     *
     * @param CategoryServices $services
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getComplainList(CategoryServices $services)
    {
        return $this->success($services->getComplainList());
    }

    /**
     * @param ChatComplainServices $services
     * @return mixed
     */
    public function complain(ChatComplainServices $services)
    {
        $data = $this->request->postMore([
            ['content', ''],
            ['user_id', ''],
            ['cate_id', []],
        ]);

        $data['cate_id'] = implode('/', $data['cate_id']);
        $services->save($data);

        return $this->success('投诉成功');
    }

    /**
     * 获取用户协议内容
     * @return mixed
     */
    public function getUserAgreement()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $content = $cache->getDbCache('user_agreement', '');
        return $this->success(compact('content'));
    }

    /**
     * 记录聊天记录
     * @param ChatServiceDialogueRecordServices $recordServices
     * @return mixed
     */
    public function savelog(ChatServiceDialogueRecordServices $recordServices)
    {
        $data = $this->request->postMore([
            ['other', []],
            ['type', 0],
            ['is_send', 0],
            ['to_user_id', 0],
            ['msn_type', 0],
            ['msn', ''],
        ]);
        if (!$data['to_user_id'] || !$data['msn']) {
            return $this->fail('缺少参数');
        }
        $data['appid'] = $this->kefuInfo['appid'];
        $data['user_id'] = $this->kefuInfo['user_id'];
        $data['add_time'] = time();
        $res = $recordServices->save($data);

        if (!$res) {
            return $this->fail('插入失败');
        }
        $res = $res->toArray();
        $res['nickname'] = $this->kefuInfo['nickname'];
        $res['avatar'] = $this->kefuInfo['avatar'];
        $res['_add_time'] = date('Y-m-d H:i:s', $data['add_time']);
        return $this->success($res);
    }
}
