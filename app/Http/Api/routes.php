<?php
/**
 * Created by PhpStorm.
 * User: Li
 * Date: 15/11/2
 * Time: 下午4:05
 */

/*
|-------------------------------------------------------------------------
|  API接口组
|-------------------------------------------------------------------------
 */
// 不需要验证token
$app->group(['prefix' => '/app/v1'], function ($app) {
    //测试
    Route::any('/test',['uses' => 'UserController@test']);
});
// 'middleware' => 'ApiAuth'需要登录后访问的
$app->group(['prefix' => '/app/v1', 'middleware' => 'ApiAuth'], function ($app) {

    //用户信息
    Route::any('/base_info',['uses' => 'UserController@baseInfo']);

});


