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

namespace app\controller\admin\chat;


use app\controller\admin\AuthController;
use app\services\chat\ChatServiceDialogueRecordServices;
use app\services\chat\ChatServiceRecordServices;
use app\services\chat\ChatServiceServices;
use app\services\chat\ChatUserServices;
use app\services\kefu\LoginServices;
use crmeb\services\CacheService;
use FormBuilder\Exception\FormBuilderException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

/**
 * Class Service
 * @package app\controller\admin\chat
 */
class Service extends AuthController
{

    /**
     * Service constructor.
     * @param ChatServiceServices $services
     */
    public function __construct(ChatServiceServices $services)
    {
        parent::__construct();
        $this->services = $services;
    }

    /**
     * 显示资源列表
     *
     * @return Response
     */
    public function index()
    {
        return $this->success($this->services->getServiceList([]));
    }

    /**
     * 添加客服表单
     * @return mixed
     * @throws FormBuilderException
     */
    public function add()
    {
        return $this->success($this->services->create());
    }

    /**
     * 保存新建的资源
     * @return mixed
     */
    public function save(ChatUserServices $services)
    {
        $data = $this->request->postMore([
            ['appid', ''],
            ['avatar', ''],
            ['customer', ''],
            ['notify', ''],
            ['phone', ''],
            ['account', ''],
            ['password', ''],
            ['true_password', ''],
            ['nickname', ''],
            ['auto_reply', 0],
            ['welcome_words', ''],
            ['status', 1],
        ]);
        if ($data['avatar'] == '') {
            return $this->fail('请选择客服头像');
        }
        if (!check_phone($data['phone'])) {
            return $this->fail('请输入正确的手机号');
        }
        if (!$data['account']) {
            return $this->fail('请输入账号');
        }
        if (!preg_match('/^[a-zA-Z0-9]{4,30}$/', $data['account'])) {
            return $this->fail('账号必须为数字或者字母的组合4-30位');
        }
        if (!$data['password']) {
            return $this->fail('请输入密码');
        }
        if (!preg_match('/^[0-9a-z_$]{6,20}$/i', $data['password'])) {
            return $this->fail('密码必须为数字或者字母的组合6-20位');
        }
        if ($this->services->count(['phone' => $data['phone']])) {
            return $this->fail('该手机号的客服已存在!');
        }
        if ($this->services->count(['account' => $data['account']])) {
            return $this->fail('该客服账号已存在!');
        }
        $data['add_time'] = time();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $res = $this->services->transaction(function () use ($data, $services) {
            $res = $this->services->save($data);
            if ($userInfo = $services->get(['phone' => $data['phone'], 'appid' => $data['appid']])) {
                $userInfo->is_kefu = 1;
                $userInfo->save();
                $res->user_id = $userInfo->id;
            } else {
                $uid = $services->max(['appid' => $data['appid']]) + 1;
                $userInfo = $services->save([
                    'phone' => $data['phone'],
                    'nickname' => $data['nickname'],
                    'avatar' => $data['avatar'],
                    'is_delete' => 0,
                    'type' => 0,
                    'uid' => $uid,
                    'appid' => $data['appid'],
                ]);
                $res->user_id = $userInfo->id;
            }
            return $res->save();
        });

        if ($res) {
            return $this->success('客服添加成功');
        } else {
            return $this->fail('客服添加失败，请稍后再试');
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return $this->success($this->services->edit((int)$id));
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return Response
     */
    public function update(ChatUserServices $services, $id)
    {
        $data = $this->request->postMore([
            ['appid', ''],
            ['avatar', ''],
            ['nickname', ''],
            ['account', ''],
            ['phone', ''],
            ['status', 1],
            ['notify', 1],
            ['customer', 1],
            ['password', ''],
            ['auto_reply', 0],
            ['welcome_words', ''],
            ['true_password', ''],
        ]);
        $customer = $this->services->get((int)$id);
        if (!$customer) {
            return $this->fail('数据不存在');
        }
        if ($data["nickname"] == '') {
            return $this->fail("客服名称不能为空！");
        }
        if (!check_phone($data['phone'])) {
            return $this->fail('请输入正确的手机号');
        }
        if ($customer['phone'] != $data['phone'] && $this->services->count(['phone' => $data['phone']])) {
            return $this->fail('该手机号的客服已存在!');
        }
        if ($data['password']) {
            if (!preg_match('/^[0-9a-z_$]{6,16}$/i', $data['password'])) {
                return $this->fail('密码必须为数字或者字母的组合');
            }
            if (!$data['true_password']) {
                return $this->fail('请输入确认密码');
            }
            if ($data['password'] != $data['true_password']) {
                return $this->fail('两次输入的密码不正确');
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        $this->services->update($id, $data);

        $update = [];
        if ($data['avatar'] != $customer->avatar) {
            $update['avatar'] = $data['avatar'];
        }
        if ($data['nickname'] != $customer->nickname) {
            $update['nickname'] = $data['nickname'];
        }
        if ($update) {
            $services->update($customer['user_id'], $update);
        }

        return $this->success('修改成功!');
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return Response
     */
    public function delete($id)
    {
        if (!$this->services->delete($id))
            return $this->fail('删除失败,请稍候再试!');
        else
            return $this->success('删除成功!');
    }

    /**
     * 修改状态
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if ($status == '' || $id == 0) return $this->fail('参数错误');
        $info = $this->services->get($id, ['status', 'user_id']);
        $info->status = $status;
        $info->save();
        return $this->success($status == 0 ? '隐藏成功' : '显示成功');
    }

    /**
     * 聊天记录
     *
     * @return Response
     */
    public function chat_user($id)
    {
        $userId = $this->services->value(['id' => $id], 'user_id');
        if (!$userId) {
            return $this->fail('数据不存在!');
        }
        return $this->success($this->services->getChatUser((int)$userId));
    }


    /**
     * 聊天记录
     * @param ChatServiceDialogueRecordServices $services
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function chat_list(ChatServiceDialogueRecordServices $services)
    {
        $data = $this->request->getMore([
            ['id', 0],
            ['to_user_id', 0],
            ['id', 0]
        ]);
        if ($data['id']) {
            CacheService::set('admin_chat_list' . $this->adminId, $data);
        }
        $data = CacheService::get('admin_chat_list' . $this->adminId);
        if ($data['id']) {
            $where = [
                'chat' => [$data['id'], $data['to_user_id']],
            ];
        } else {
            $where = [];
        }
        $list = $services->getChatLogList($where);
        return $this->success($list);
    }

    /**
     * 客服登录
     * @param LoginServices $services
     * @param $id
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function keufLogin(LoginServices $services, $id)
    {
        $serviceInfo = $services->get($id);
        if (!$serviceInfo) {
            return $this->fail('登录的客服不存在');
        }
        if (!$serviceInfo->account || !$serviceInfo->password) {
            return $this->fail('请先填写客服账号和密码再尝试进入客服平台');
        }
        if (!$serviceInfo->status) {
            return $this->fail('客服帐号已被禁用');
        }
        return $this->success($services->authLogin($serviceInfo->account));
    }
}
