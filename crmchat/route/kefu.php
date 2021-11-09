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

use app\http\middleware\AllowOriginMiddleware;
use app\http\middleware\InstallMiddleware;
use app\http\middleware\kefu\KefuAuthTokenMiddleware;
use think\facade\Config;
use think\facade\Route;
use think\Response;

Route::group('api', function () {

    Route::group('kefu', function () {

        Route::any('ticket/[:appid]', 'Login/ticket');

        Route::group(function () {

            Route::post('login', 'Login/login')->name('kefuLogin');//账号登录
            Route::get('key', 'Login/getLoginKey')->name('getLoginKey');//获取扫码登录key
            Route::get('scan/:key', 'Login/scanLogin')->name('scanLogin');//检测扫码情况
            Route::get('config', 'Login/getAppid')->name('getAppid');//获取配置
            Route::get('agreement', 'User/getUserAgreement')->name('getUserAgreement');//用户协议

            Route::group(function () {

                Route::post('upload', 'User/upload')->name('upload');//上传图片

            })->middleware(KefuAuthTokenMiddleware::class);

            Route::group('user', function () {

                Route::get('list', 'User/getUserList')->name('getUserList');//获取当前客服的客户列表
                Route::get('record', 'User/recordList')->name('recordList');//和客服聊天过的用户
                Route::get('count', 'User/getMessageCount')->name('getMessageCount');//未读条数
                Route::get('info/:userId', 'User/userInfo')->name('getUserInfo');//用户详细信息
                Route::get('label', 'User/getUserLabel')->name('getUserLabel');//用户标签
                Route::get('label/all', 'User/getLabelAll')->name('getLabelAll');//所有用户标签
                Route::put('label/:userId', 'User/setUserLabel')->name('setUserLabel');//设置用户标签
                Route::delete('label/:userId/:labelId', 'User/delUserLabel')->name('delUserLabel');//删除单个用户标签
                Route::get('group', 'User/getUserGroup')->name('getUserGroup');//退出登录
                Route::put('group/:userId/:id', 'User/setUserGroup')->name('setUserGroup');//退出登录
                Route::post('logout', 'User/logout')->name('logout');//退出登录
                Route::get('userInfo', 'User/getKefuInfo')->name('getKefuInfo');//获取当前客服信息
                Route::put('userInfo', 'User/updateKefu')->name('updateKefu');//修改当前客服信息
                Route::put('updateUser/:userId', 'User/updateUser')->name('updateUser');//修改用户信息
                Route::put('client', 'User/updateService')->name('updateService');//修改当前客服client_id
                Route::post('feedback', 'User/saveFeedback')->name('saveFeedback');//修改客服反馈
                Route::get('complain', 'User/getComplainList')->name('getComplainList');//获取客服投诉
                Route::post('complain', 'User/complain')->name('complain');//保存客户投诉
                Route::put('status/:userId', 'User/status')->name('status');//拉黑用户
                Route::post('savelog', 'User/savelog')->name('savelog');//保存聊天记录

            })->middleware(KefuAuthTokenMiddleware::class);

            Route::group('statistics', function () {

                Route::get('all', 'Statistics/sum')->name('kefuStatisticsSum');//获取当前客服统计数据(头部数量)
                Route::get('index', 'Statistics/index')->name('kefuStatisticsIndex');//获取当前客服统计数据

            })->middleware(KefuAuthTokenMiddleware::class);

            Route::group('service', function () {

                Route::post('code', 'Service/setLoginCode')->name('setLoginCode');//扫码登陆
                Route::get('list', 'Service/getChatList')->name('getChatList');//聊天记录
                Route::get('info', 'Service/getServiceInfo')->name('getServiceInfo');//客服详细信息
                Route::get('speechcraft', 'Service/getSpeechcraftList')->name('getSpeechcraftList');//客服话术
                Route::post('transfer', 'Service/transfer')->name('transfer');//客服转接
                Route::get('transfer_list', 'Service/getServiceList')->name('getServiceList');//客服转接
                Route::get('cate', 'Service/getCateList')->name('getCateList');//分类列表
                Route::post('cate', 'Service/saveCate')->name('saveCate');//保存分类
                Route::put('cate/:id', 'Service/editCate')->name('editCate');//编辑分类
                Route::delete('cate/:id', 'Service/deleteCate')->name('deleteCate');//删除分类
                Route::post('speechcraft', 'Service/saveSpeechcraft')->name('saveSpeechcraft');//添加话术
                Route::put('speechcraft/:id', 'Service/editSpeechcraft')->name('editSpeechcraft');//修改话术
                Route::delete('speechcraft/:id', 'Service/deleteSpeechcraft')->name('deleteSpeechcraft');//删除话术
                Route::get('auth_reply', 'Service/getAuthReply')->name('getAuthReply');//获取当前自动回复内容
                Route::post('auth_reply/:id', 'Service/saveAuthReply')->name('saveAuthReply');//保存当前自动回复内容
                Route::delete('auth_reply/:id', 'Service/deleteAuthReply')->name('deleteAuthReply');//删除当前自动回复内容
                Route::put('auth_reply/:value', 'Service/setAutoReply')->name('setAutoReply');//设置是否自动回复
                Route::put('backstage/:backstage', 'Service/backstage')->name('backstage');//设置是否后台运行

            })->middleware(KefuAuthTokenMiddleware::class);

        });

        Route::miss(function () {
            if (app()->request->isOptions()) {
                $header = Config::get('cookie.header');
                $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
                return Response::create('ok')->code(200)->header($header);
            } else
                return Response::create()->code(404);
        });

    })->prefix('kefu.');

})->middleware([AllowOriginMiddleware::class, InstallMiddleware::class]);
