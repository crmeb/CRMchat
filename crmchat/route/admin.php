<?php

use app\http\middleware\admin\AdminAuthTokenMiddleware;
use app\http\middleware\admin\AdminCkeckRoleMiddleware;
use app\http\middleware\admin\AdminLogMiddleware;
use app\http\middleware\AllowOriginMiddleware;
use app\http\middleware\InstallMiddleware;
use app\http\middleware\kefu\KefuAuthTokenMiddleware;
use app\http\middleware\mobile\MobileAuthTokenMiddleware;
use think\facade\Config;
use think\facade\Route;
use think\Response;

Route::group('api', function () {

    Route::group('admin', function () {
        /**
         * 无需授权的接口
         */
        Route::group(function () {
            //用户名密码登录
            Route::post('login', 'Login/login')->name('AdminLogin')->option(['real_name' => '登录']);
            //后台登录页面数据
            Route::get('login/info', 'Login/info')->option(['real_name' => '获取登录信息']);
            //下载文件
            Route::get('download', 'PublicController/download')->option(['real_name' => '下载文件']);
            //验证码
            Route::get('captcha_pro', 'Login/captcha')->name('')->option(['real_name' => '获取验证码']);

            Route::get('index', 'Test/index')->option(['real_name' => '测试地址']);

            Route::get('r', 'Test/rule')->option(['real_name' => '路由地址']);

        });

        /**
         * 需要授权的接口
         */
        Route::group(function () {

            Route::get('logo', 'Index/logo')->option(['real_name' => '获取logo']);
            Route::get('jnotice', 'Index/jnotice')->option(['real_name' => '消息通知']);
            Route::get('menusList', 'Index/getMenusList')->option(['real_name' => '获取菜单']);
            Route::put('app/reset/:id', 'Application/reset')->option(['real_name' => '重置token']);

            Route::resource('app', 'Application')->option(['real_name' => [
                'index' => '获取应用列表接口',
                'create' => '获取应用创建接口',
                'save' => '保存应用接口',
                'edit' => '获取修改应用接口',
                'update' => '修改应用接口',
                'delete' => '删除应用接口'
            ]])->except(['read']);

            //客户统计
            Route::get('chart/sum', 'Index/sum')->option(['real_name' => '客户统计']);
            //客户首页统计
            Route::get('chart', 'Index/index')->option(['real_name' => '客户首页统计']);

        })->middleware([
            AdminAuthTokenMiddleware::class,
            AdminCkeckRoleMiddleware::class,
            AdminLogMiddleware::class
        ]);

        /**
         * 客服 相关路由
         */
        Route::group('chat', function () {
            //客服列表
            Route::get('kefu', 'Service/index')->option(['real_name' => '客服列表']);
            //自动回复列表
            Route::get('reply', 'AutoReply/index')->option(['real_name' => '自动回复列表']);
            //获取自动回复表单
            Route::get('reply/:id', 'AutoReply/create')->option(['real_name' => '获取自动回复表单']);
            //保存自动回复
            Route::post('reply/:id', 'AutoReply/save')->option(['real_name' => '保存自动回复']);
            //删除自动回复
            Route::delete('reply/:id', 'AutoReply/delete')->option(['real_name' => '删除自动回复']);
            //客服登录
            Route::get('kefu/login/:id', 'Service/keufLogin')->option(['real_name' => '客服登录']);
            //添加客服表单
            Route::get('kefu/add', 'Service/add')->option(['real_name' => '添加客服表单']);
            //添加客服
            Route::post('kefu', 'Service/save')->option(['real_name' => '添加客服']);
            //修改客服表单
            Route::get('kefu/:id/edit', 'Service/edit')->option(['real_name' => '修改客服表单']);
            //修改客服
            Route::put('kefu/:id', 'Service/update')->option(['real_name' => '修改客服']);
            //删除客服
            Route::delete('kefu/:id', 'Service/delete')->option(['real_name' => '删除客服']);
            //修改客服状态
            Route::put('kefu/set_status/:id/:status', 'Service/set_status')->option(['real_name' => '修改客服状态']);
            //聊天记录
            Route::get('kefu/record/:id', 'Service/chat_user')->option(['real_name' => '聊天记录']);
            //查看对话
            Route::get('kefu/chat_list', 'Service/chat_list')->option(['real_name' => '查看对话']);
            //客服话术资源路由
            Route::resource('speechcraft', 'ServiceSpeechcraft')->option(['real_name' => [
                'index' => '获取话术列表接口',
                'create' => '获取话术创建接口',
                'read' => '获取话术详情接口',
                'save' => '保存话术接口',
                'edit' => '获取修改话术接口',
                'update' => '修改话术接口',
                'delete' => '删除话术接口'
            ]]);
            //客服话术分类资源路由
            Route::resource('speechcraftcate', 'ServiceSpeechcraftCate')->option(['real_name' => [
                'index' => '获取话术分类列表接口',
                'create' => '获取话术分类创建接口',
                'read' => '获取话术分类详情接口',
                'save' => '保存话术分类接口',
                'edit' => '获取修改话术分类接口',
                'update' => '修改话术分类接口',
                'delete' => '删除话术分类接口'
            ]]);
            //用户反馈资源路由
            Route::resource('feedback', 'ServiceFeedback')->only(['index', 'delete', 'update', 'edit'])->option(['real_name' => [
                'index' => '获取用户反馈列表接口',
                'edit' => '获取修改用户反馈接口',
                'update' => '修改用户反馈接口',
                'delete' => '删除用户反馈接口'
            ]])->except(['save', 'create', 'read']);

        })->middleware([
            AdminAuthTokenMiddleware::class,
            AdminCkeckRoleMiddleware::class,
            AdminLogMiddleware::class
        ])->prefix('admin.chat.');

        Route::group('user', function () {

            Route::get('/index', 'User/index')->option(['real_name' => '用户列表']);
            Route::get('/user_label', 'User/getLavelAll')->option(['real_name' => '用户标签搜索列表']);
            Route::get('/edit/:id', 'User/edit')->option(['real_name' => '获取修改用户表单']);
            Route::put('/update/:id', 'User/update')->option(['real_name' => '修改用户']);
            Route::put('/batch/label', 'User/batchLabel')->option(['real_name' => '批量修改用户标签']);
            Route::put('/batch/group', 'User/batchGroup')->option(['real_name' => '批量修改用户分组']);
            Route::get('/label/all', 'User/getLabelAll')->option(['real_name' => '获取全部标签']);
            Route::get('/group/all', 'User/getGroupAll')->option(['real_name' => '获取全部分组']);

            Route::resource('label/cate', 'LabelCate')->option(['real_name' => [
                'index' => '获取标签分类列表接口',
                'create' => '获取标签分类创建接口',
                'save' => '保存标签分类接口',
                'edit' => '获取修改标签分类接口',
                'update' => '修改标签分类接口',
                'delete' => '删除标签分类接口'
            ]])->except(['read']);

            Route::resource('label', 'Label')->option(['real_name' => [
                'index' => '获取标签列表接口',
                'create' => '获取标签创建接口',
                'save' => '保存标签接口',
                'edit' => '获取修改标签接口',
                'update' => '修改标签接口',
                'delete' => '删除标签接口'
            ]])->except(['read']);

            Route::resource('group', 'Group')->option(['real_name' => [
                'index' => '获取分组列表接口',
                'create' => '获取分组创建接口',
                'save' => '保存分组接口',
                'edit' => '获取修分组签接口',
                'update' => '修改分组接口',
                'delete' => '删除分组接口'
            ]])->except(['read']);


        })->middleware([
            AdminAuthTokenMiddleware::class,
            AdminCkeckRoleMiddleware::class,
            AdminLogMiddleware::class
        ])->prefix('admin.user.');

        /**
         * 附件相关路由
         */
        Route::group('file', function () {
            //图片附件列表
            Route::get('file', 'Attachment/index')->option(['real_name' => '图片附件列表']);
            //删除图片
            Route::post('file/delete', 'Attachment/delete')->option(['real_name' => '删除图片']);
            //移动图片分类表单
            Route::get('file/move', 'Attachment/move')->option(['real_name' => '移动图片分类表单']);
            //移动图片分类
            Route::put('file/do_move', 'Attachment/moveImageCate')->option(['real_name' => '移动图片分类']);
            //修改图片名称
            Route::put('file/update/:id', 'Attachment/update')->option(['real_name' => '修改图片名称']);
            //上传图片
            Route::post('upload/[:upload_type]', 'Attachment/upload')->option(['real_name' => '上传图片']);
            //附件分类管理资源路由
            Route::resource('category', 'AttachmentCategory')->option([
                'real_name' => [
                    'index' => '获取附件分类列表接口',
                    'create' => '获取附件分类创建接口',
                    'read' => '获取附件分类详情接口',
                    'save' => '保存附件分类接口',
                    'edit' => '获取修改附件分类接口',
                    'update' => '修改附件分类接口',
                    'delete' => '删除附件分类接口'
                ]
            ]);

        })->middleware([
            AdminAuthTokenMiddleware::class,
            AdminCkeckRoleMiddleware::class,
            AdminLogMiddleware::class
        ])->prefix('admin.file.');

        Route::group('system', function () {
            //系统日志
            Route::get('log', 'system.Log/index')->name('SystemLog')->option(['real_name' => '系统日志']);
            //系统日志管理员搜索条件
            Route::get('log/search_admin', 'system.Log/search_admin')->option(['real_name' => '系统日志管理员搜索条件']);

        })->middleware([
            AdminAuthTokenMiddleware::class,
            AdminCkeckRoleMiddleware::class,
            AdminLogMiddleware::class
        ]);
        /**
         * 系统设置维护 系统权限管理、系统菜单管理 系统配置 相关路由
         */
        Route::group('setting', function () {

            //管理员退出登陆
            Route::get('admin/logout', 'system.Admin/logout')->name('SystemAdminLogout')->option(['real_name' => '退出登陆']);
            //修改管理员状态
            Route::put('set_status/:id/:status', 'system.Admin/set_status')->name('SystemAdminSetStatus')->option(['real_name' => '修改管理员状态']);
            //获取当前管理员信息
            Route::get('info', 'system.Admin/info')->name('SystemAdminInfo')->option(['real_name' => '获取当前管理员信息']);
            //修改当前管理员信息
            Route::put('update_admin', 'system.Admin/update_admin')->name('SystemAdminUpdateAdmin')->option(['real_name' => '修改当前管理员信息']);
            //修改权限规格显示状态
            Route::put('menus/show/:id', 'system.Menus/show')->name('SystemMenusShow')->option(['real_name' => '修改权限规格显示状态']);
            //管理员身份列表
            Route::get('role', 'system.Role/index')->option(['real_name' => '管理员身份列表']);
            //管理员身份权限列表
            Route::get('role/create', 'system.Role/create')->option(['real_name' => '管理员身份权限列表']);
            //编辑管理员详情
            Route::get('role/:id/edit', 'system.Role/edit')->option(['real_name' => '编辑管理员详情']);
            //新建或编辑管理员
            Route::post('role/:id', 'system.Role/save')->option(['real_name' => '新建或编辑管理员']);
            //修改管理员身份状态
            Route::put('role/set_status/:id/:status', 'system.Role/set_status')->option(['real_name' => '修改管理员身份状态']);
            //删除管理员身份
            Route::delete('role/:id', 'system.Role/delete')->option(['real_name' => '删除管理员身份']);
            //修改配置分类状态
            Route::put('config_class/set_status/:id/:status', 'system.ConfigTab/set_status')->option(['real_name' => '修改配置分类状态']);
            //修改配置状态
            Route::put('config/set_status/:id/:status', 'system.Config/set_status')->option(['real_name' => '修改配置状态']);
            //基本配置编辑头部数据
            Route::get('config/header_basics', 'system.Config/header_basics')->option(['real_name' => '基本配置编辑头部数据']);
            //基本配置编辑表单
            Route::get('config/edit_basics', 'system.Config/edit_basics')->option(['real_name' => '基本配置编辑表单']);
            //基本配置保存数据
            Route::post('config/save_basics', 'system.Config/save_basics')->option(['real_name' => '基本配置保存数据']);
            //基本配置上传文件
            Route::post('config/upload', 'system.Config/file_upload')->option(['real_name' => '基本配置上传文件']);
            //组合数据全部
            Route::get('group_all', 'system.Group/getGroup')->option(['real_name' => '组合数据全部']);
            //组合数据头部
            Route::get('group_data/header', 'system.GroupData/header')->option(['real_name' => '组合数据头部']);
            //修改组合数据状态
            Route::put('group_data/set_status/:id/:status', 'system.GroupData/set_status')->option(['real_name' => '修改组合数据状态']);
            //获取客服广告
            Route::get('get_kf_adv', 'system.GroupData/getKfAdv')->option(['real_name' => '获取客服广告']);
            //设置客服广告
            Route::post('set_kf_adv', 'system.GroupData/setKfAdv')->option(['real_name' => '设置客服广告']);
            //获取隐私协议
            Route::get('get_user_agreement', 'system.GroupData/getUserAgreement')->option(['real_name' => '获取隐私协议']);
            //设置隐私协议
            Route::post('set_user_agreement', 'system.GroupData/setUserAgreement')->option(['real_name' => '设置隐私协议']);
            //未添加的权限规则列表
            Route::get('ruleList', 'system.Menus/ruleList')->option(['real_name' => '未添加的权限规则列表']);
            //组合数据资源路由
            Route::resource('group', 'system.Group')->option(['real_name' => [
                'index' => '获取话组合数据列表接口',
                'create' => '获取组合数据创建接口',
                'read' => '获取组合数据详情接口',
                'save' => '保存组合数据接口',
                'edit' => '获取修改组合数据接口',
                'update' => '修改组合数据接口',
                'delete' => '删除组合数据接口'
            ]]);
            //组合数据子数据资源路由
            Route::resource('group_data', 'system.GroupData')->option(['real_name' => [
                'index' => '获取组合数据资源列表接口',
                'create' => '获取组合数据资源创建接口',
                'read' => '获取组合数据资源详情接口',
                'save' => '保存组合数据资源接口',
                'edit' => '获取修改组合数据资源接口',
                'update' => '修改组合数据资源接口',
                'delete' => '删除组合数据资源接口'
            ]]);
            //系统配置分类资源路由
            Route::resource('config_class', 'system.ConfigTab')->option(['real_name' => [
                'index' => '获取配置分类列表接口',
                'create' => '获取配置分类创建接口',
                'read' => '获取配置分类详情接口',
                'save' => '保存配置分类接口',
                'edit' => '获取修改配置分类接口',
                'update' => '修改配置分类接口',
                'delete' => '删除配置分类接口'
            ]]);;
            //系统配置资源路由
            Route::resource('config', 'system.Config')->option([
                'real_name' => [
                    'index' => '获取系统配置列表接口',
                    'create' => '获取系统配置创建接口',
                    'save' => '保存系统配置接口',
                    'edit' => '获取修改系统配置接口',
                    'update' => '修改系统配置接口',
                    'delete' => '删除系统配置接口'
                ]
            ])->except(['read']);
            //权限菜单资源路由
            Route::resource('menus', 'system.Menus')->option([
                'real_name' => [
                    'index' => '获取权限菜单列表接口',
                    'create' => '获取权限菜单创建接口',
                    'read' => '获取权限菜单详情接口',
                    'save' => '保存权限菜单接口',
                    'edit' => '获取修改权限菜单接口',
                    'update' => '修改权限菜单接口',
                    'delete' => '删除权限菜单接口'
                ]
            ]);
            //管理员资源路由
            Route::resource('admin', 'system.Admin')->option([
                'real_name' => [
                    'index' => '获取管理员列表接口',
                    'create' => '获取管理员创建接口',
                    'save' => '保存管理员接口',
                    'edit' => '获取修改管理员接口',
                    'update' => '修改管理员接口',
                    'delete' => '删除管理员接口'
                ]
            ])->except(['read']);
        })->middleware([
            AdminAuthTokenMiddleware::class,
            AdminCkeckRoleMiddleware::class,
            AdminLogMiddleware::class
        ]);

        Route::miss(function () {
            if (app()->request->isOptions()) {
                $header = Config::get('cookie.header');
                $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
                return Response::create('ok')->code(200)->header($header);
            } else
                return Response::create()->code(404);
        });

    })->prefix('admin.');

})->middleware([AllowOriginMiddleware::class, InstallMiddleware::class]);
