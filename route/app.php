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


Route::group('user', function (){
    Route::get('login', 'Login/index');
    Route::post('doLogin', 'Login/doLogin');
});


Route::group('home', function (){
    Route::get('index', 'Index/index');
    Route::get('onLine', 'Index/onLineUser');
    Route::post('recent', 'Index/recent');
});

Route::group('mq', function (){
    Route::get('direct/producer', '\app\controller\direct\Producer@index');
    Route::get('direct/consumer', '\app\controller\direct\Consumer@index');

    Route::get('fanout/producer', '\app\controller\fanout\Producer@index');
    Route::get('fanout/consumer', '\app\controller\fanout\Consumer@index');

    Route::get('topic/producer', '\app\controller\topic\Producer@index');
    Route::get('topic/consumer', '\app\controller\topic\Consumer@index');

    Route::get('work/fair/producer', '\app\controller\work\fair\Producer@index');
    Route::get('work/fair/consumer', '\app\controller\work\fair\Consumer@index');

    Route::get('work/lunxun/producer', '\app\controller\work\lunxun\Producer@index');
    Route::get('work/lunxun/consumer', '\app\controller\work\lunxun\Consumer@index');

    Route::get('ttl/producer', '\app\controller\ttl\Producer@index');
    Route::get('ttl/producer_quene', '\app\controller\ttl\ProducerExpiredQuene@index');

    Route::get('dead_letter/producer', '\app\controller\dead_letter\Producer@index');
});

Route::get('test', '\app\controller\direct\test@index');

Route::group('es', function (){
    Route::resource('db', 'app\controller\es\EsDb');


    Route::resource('doc', 'app\controller\es\EsDoc');
    Route::post('doc/add_or_edit', 'app\controller\es\EsDoc@createOrEdit');
    Route::post('doc/bulk', 'app\controller\es\EsDoc@bulk');
/*    Route::put('doc/<id?>', 'app\controller\es\EsDoc@update');*/
});

Route::group('es/goods', function (){
    Route::get('doc', 'app\controller\es\goods\Index@index');
    Route::resource('db', 'app\controller\es\goods\GoodsDb');
});
