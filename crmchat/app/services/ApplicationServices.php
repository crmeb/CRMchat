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

namespace app\services;


use app\dao\ApplicationDao;
use app\services\chat\ChatUserServices;
use crmeb\basic\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\AuthException;
use crmeb\services\CacheService;
use crmeb\services\FormBuilder;
use crmeb\utils\Arr;
use crmeb\utils\Encrypter;
use FormBuilder\Exception\FormBuilderException;
use Psr\SimpleCache\InvalidArgumentException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class ApplicationServices
 * @package app\services
 */
class ApplicationServices extends BaseServices
{

    /**
     * ApplicationServices constructor.
     * @param ApplicationDao $dao
     */
    public function __construct(ApplicationDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取应用列表数据
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getDataList($where, ['*'], 'id', $page, $limit);
        $count = $this->dao->count();
        return compact('list', 'count');
    }

    /**
     * 获取表单规则
     * @param array $data
     * @return array
     */
    public function getFormRule(array $data = [])
    {
        return [
            FormBuilder::frameImage('icon', '应用图标', $this->url('admin/widget.images/index', ['fodder' => 'icon'], true), $data['value'])
                ->icon('ios-image')->width('950px')->height('420px')->info($data['desc'])->col(13)->required(),
            FormBuilder::input('name', '应用名称', $data['name'] ?? '')->required(),
            FormBuilder::textarea('introduce', '应用简介', $data['introduce'] ?? ''),
        ];
    }

    /**
     * 获取创建表单
     * @return array
     * @throws FormBuilderException
     */
    public function getCreateForm()
    {
        return create_form('添加应用', $this->getFormRule(), $this->url('admin/app'), 'post');
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
    public function getUpdateForm(int $id)
    {
        $appInfo = $this->dao->get($id);
        if (!$appInfo) {
            throw new AdminException('修改的应用不存在');
        }
        return create_form('修改应用', $this->getFormRule($appInfo->toArray()), $this->url('admin/app', ['id' => $id]), 'put');
    }

    /**
     * 获取
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getOptions()
    {
        return $this->dao->getDataList(['is_delete' => 0], ['name as label', 'appid as value'], 'id');
    }

    /**
     * 创建或者更新用户
     * @param string $appid
     * @param array $userData
     * @return array|mixed
     * @throws InvalidArgumentException
     */
    public function createUser(string $appid, array $userData = [])
    {
        $uid = $userData['uid'] ?? 0;
        $nickname = $userData['nickname'] ?? '';
        $avatar = $userData['avatar'] ?? '';
        $phone = $userData['phone'] ?? '';
        $openid = $userData['openid'] ?? '';
        $type = $userData['type'] ?? 0;

        $redis = CacheService::redisHandler();
        if ($userInfo = $redis->get($appid . '-' . $uid)) {
            return $userInfo;
        }

        /** @var ChatUserServices $userServices */
        $userServices = app()->make(ChatUserServices::class);
        if ($uid && ($userInfo = $userServices->get(['uid' => $uid, 'appid' => $appid]))) {
            $userInfo->nickname = $nickname;
            $userInfo->avatar = $avatar;
            $userInfo->phone = $phone;
            $userInfo->openid = $openid;
            $userInfo->type = $type;
            $userInfo->save();

            $redis->set($appid . '-' . $uid, $userInfo->toArray(), 86400);

        } else {
            $isTourist = 0;
            //游客模式
            if ((int)$uid === 0) {
                $isTourist = 1;
                mt_srand();
                $rand1 = mt_rand(10, 99);
                mt_srand();
                $rand2 = mt_rand(1000, 9999);
                $uid = date('Y') . $rand1 . $rand2;
            }
            if (!$nickname) {
                $nickname = '游客' . $uid;
            }
            if (!$avatar) {
                $touristAvatar = sys_config('tourist_avatar');
                $avatar = Arr::getArrayRandKey(is_array($touristAvatar) ? $touristAvatar : []);
                $avatar = link_url($avatar);
            }
            $userInfo = $userServices->save([
                'uid' => $uid,
                'nickname' => $nickname,
                'avatar' => $avatar,
                'phone' => $phone,
                'appid' => $appid,
                'openid' => $openid,
                'type' => $type,
                'is_tourist' => $isTourist,
            ]);
            if (!$userInfo) {
                throw new AuthException('创建用户失败');
            }
            $redis->set($appid . '-' . $uid, $userInfo->toArray(), 86400);
        }
        return $userInfo->toArray();
    }

    /**
     * @param string $token
     * @param array $other
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws InvalidArgumentException
     * @throws AuthException
     */
    public function parseToken(string $token, array $other = [])
    {
        if (strlen($token) === 32) {
            $token = $this->dao->value(['token_md5' => $token], 'token');
        }
        /** @var Encrypter $encrypter */
        $encrypter = app()->make(Encrypter::class);

        try {
            $appInfo = $encrypter->decrypt($token);
        } catch (\Throwable $e) {
            throw new AuthException('无效TOKEN');
        }

        $appInfo = json_decode($appInfo, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AuthException('验签失败');
        }

        if (!isset($appInfo['appid'])) {
            throw new AuthException('缺少应用ID');
        }

        $appData = $this->dao->get(['appid' => $appInfo['appid'], 'is_delete' => 0]);

        if (!$appData) {
            throw new AuthException('应用不存在');
        }

        $appSecret = md5($appData['appid'] . $appData['timestamp'] . $appData['rand']);
        if ($appSecret !== $appInfo['app_secret']) {
            throw new AuthException('错误的app_secret值');
        }
        if ($other) {
            return ['user' => $this->createUser($appData['appid'], $other), 'appInfo' => $appInfo];
        } else {
            return ['appInfo' => $appInfo];
        }
    }
}
