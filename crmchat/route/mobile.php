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

//移动端 H5/小程序/APP
use app\http\middleware\AllowOriginMiddleware;
use app\http\middleware\InstallMiddleware;
use app\http\middleware\mobile\MobileAuthTokenMiddleware;
use think\facade\Config;
use think\facade\Route;
use think\Response;


Route::group('api', function () {

    Route::group('mobile', function () {

        Route::group('user', function () {

            Route::get('record', 'Service/getRecordList');

        })->middleware(MobileAuthTokenMiddleware::class);

        Route::group('service', function () {

            Route::get('adv', 'Service/getKfAdv')->name('getKfAdv');//获取客服广告
            Route::post('feedback', 'Feedback/saveFeedback')->name('saveFeedback');//保存客服反馈内容
            Route::get('feedback', 'Feedback/getFeedbackInfo')->name('getFeedbackInfo');//获取反馈页面广告位内容
            Route::post('upload', 'Service/upload')->name('upload');//图片上传
            Route::get('cache/:key', 'Service/getCache')->name('getCache');//获取缓存
            Route::post('cache', 'Service/setCache')->name('setCache');//设置缓存

        })->middleware(MobileAuthTokenMiddleware::class);

        Route::miss(function () {
            if (app()->request->isOptions()) {
                $header                                = Config::get('cookie.header');
                $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
                return Response::create('ok')->code(200)->header($header);
            } else
                return Response::create()->code(404);
        });

    })->prefix('mobile.');

})->middleware([AllowOriginMiddleware::class, InstallMiddleware::class]);
