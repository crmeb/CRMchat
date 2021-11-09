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
namespace app\controller\admin\system;


use app\controller\admin\AuthController;
use app\services\system\SystemMenusServices;
use think\facade\Route;
use think\helper\Str;

/**
 * 菜单权限
 * Class SystemMenus
 * @package app\controller\admin\v1\setting
 */
class Menus extends AuthController
{
    /**
     * SystemMenus constructor.
     * @param SystemMenusServices $services
     */
    public function __construct(SystemMenusServices $services)
    {
        parent::__construct();
        $this->services = $services;
        $this->request->filter(['addslashes', 'trim']);
    }

    /**
     * 菜单展示列表
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['is_show', ''],
            ['keyword', ''],
        ]);
        return $this->success($this->services->getList($where));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {

        return $this->success($this->services->createMenus());
    }

    /**
     * 保存菜单权限
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->getMore([
            ['menu_name', ''],
            ['controller', ''],
            ['module', 'admin'],
            ['action', ''],
            ['icon', ''],
            ['params', ''],
            ['path', []],
            ['menu_path', ''],
            ['api_url', ''],
            ['methods', ''],
            ['unique_auth', ''],
            ['header', ''],
            ['is_header', 0],
            ['pid', 0],
            ['sort', 0],
            ['auth_type', 0],
            ['access', 1],
            ['is_show', 0],
            ['is_show_path', 0],
        ]);

        if (!$data['menu_name'])
            return $this->fail('请填写按钮名称');
        $data['path'] = implode('/', $data['path']);
        if ($this->services->save($data)) {
            return $this->success('添加成功');
        } else {
            return $this->fail('添加失败');
        }
    }

    /**
     * 获取一条菜单权限信息
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {

        if (!$id) {
            return $this->fail('数据不存在');
        }
        return $this->success($this->services->find((int)$id));
    }

    /**
     * 修改菜单权限表单获取
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!$id) {
            return $this->fail('缺少修改参数');
        }
        return $this->success($this->services->updateMenus((int)$id));
    }

    /**
     * 修改菜单
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        if (!$id || !($menu = $this->services->get($id)))
            return $this->fail('数据不存在');
        $data = $this->request->postMore([
            'menu_name',
            'controller',
            ['module', 'admin'],
            'action',
            'params',
            ['icon', ''],
            ['menu_path', ''],
            ['api_url', ''],
            ['methods', ''],
            ['unique_auth', ''],
            ['path', []],
            ['sort', 0],
            ['pid', 0],
            ['is_header', 0],
            ['header', ''],
            ['auth_type', 0],
            ['access', 1],
            ['is_show', 0],
            ['is_show_path', 0],
        ]);
        if (!$data['menu_name'])
            return $this->fail('请输入按钮名称');
        $data['path'] = implode('/', $data['path']);
        if ($this->services->update($id, $data))
            return $this->success('修改成功');
        else
            return $this->fail('修改失败');
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$id) {
            return $this->fail('参数错误，请重新打开');
        }

        if (!$this->services->delete((int)$id)) {
            return $this->fail('删除失败,请稍候再试!');
        } else {
            return $this->success('删除成功!');
        }
    }

    /**
     * 显示和隐藏
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        if (!$id) {
            return $this->fail('参数错误，请重新打开');
        }

        [$show] = $this->request->postMore([['is_show', 0]], true);

        if ($this->services->update($id, ['is_show' => $show])) {
            return $this->success('修改成功');
        } else {
            return $this->fail('修改失败');
        }
    }

    /**
     * 获取菜单数据
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function menus()
    {
        [$menus, $unique] = $this->services->getMenusList($this->adminInfo['roles'], (int)$this->adminInfo['level']);
        return $this->success(['menus' => $menus, 'unique' => $unique]);
    }

    /**
     * 获取接口列表
     * @return array
     */
    public function ruleList()
    {
        $this->app->route->setTestMode(true);
        $this->app->route->clear();
        $path = $this->app->getRootPath() . 'route' . DIRECTORY_SEPARATOR;
        $files = is_dir($path) ? scandir($path) : [];
        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                include $path . $file;
            }
        }
        $ruleList = $this->app->route->getRuleList();
        $ruleNewList = [];
        foreach ($ruleList as $item) {
            if (Str::contains($item['rule'], 'api/admin')) {
                $ruleNewList[] = $item;
            }
        }
        foreach ($ruleNewList as $key => &$value) {
            $only = $value['option']['only'] ?? [];
            $route = is_string($value['route']) ? explode('/', $value['route']) : [];
            $value['route'] = is_string($value['route']) ? $value['route'] : '';
            $action = $route[count($route) - 1] ?? null;
            if ($only && $action && !in_array($action, $only)) {
                unset($ruleNewList[$key]);
            }
            $except = $value['option']['except'] ?? [];
            if ($except && $action && in_array($action, $except)) {
                unset($ruleNewList[$key]);
            }
        }
        $ruleList = $ruleNewList;
        $menuApiList = $this->services->getColumn(['auth_type' => 2, 'is_del' => 0], "concat(`api_url`,'_',lower(`methods`)) as rule");
        if ($menuApiList) $menuApiList = array_column($menuApiList, 'rule');
        $list = [];
        $allAction = ['delete', 'index', 'update', 'edit', 'save', 'create', 'read'];
        foreach ($ruleList as $item) {
            if (!in_array($item['rule'] . '_' . $item['method'], $menuApiList)) {
                $option = $item['option']['real_name'] ?? null;
                if (is_array($option)) {
                    foreach ($allAction as $action) {
                        if (Str::contains($item['route'], $action)) {
                            $real_name = $option[$action] ?? '';
                        }
                    }
                } else {
                    $real_name = $option;
                }

                $item['real_name'] = $real_name;
                unset($item['option']);
                $item['method'] = strtoupper($item['method']);
                $list[] = $item;
            }
        }

        return $this->success($list);
    }
}
