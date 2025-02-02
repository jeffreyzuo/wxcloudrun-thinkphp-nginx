<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// 获取当前计数
Route::get('/api/count', 'index/getCount');

// 更新计数，自增或者清零
Route::post('/api/count', 'index/updateCount');

//接收微信服务管家转发过来的消息
Route::post('/api/msg_service/text/<appid>','msgService/text');
Route::post('/api/msg_service/image/<appid>','msgService/image');
Route::post('/api/msg_service/voice/<appid>','msgService/voice');
Route::post('/api/msg_service/video/<appid>','msgService/video');


