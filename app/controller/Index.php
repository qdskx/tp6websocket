<?php
namespace app\controller;

use app\BaseController;
use think\facade\Cache;
use think\facade\Session;
use think\facade\View;
use app\Model\Users;

class Index extends BaseController
{
    public function index(){
        $uid = Session::get('uid');
        if(!$uid)return View::fetch('/login');

        //TODO 在线用户列表
        $redis = Cache::store('redis')->handler();
        $onLineUserId = $redis->hkeys('uid_to_fd');
        $onLineUser = Users::whereIn('id', $onLineUserId)->where('id','<>',$uid)->select()->toArray();

        //TODO 与某一用户的最近聊天记录


        //TODO 当前用户信息
        $userinfo = Users::find($uid);
        return View::fetch('/index', ['userInfo'=>$userinfo, 'onLine'=>$onLineUser]);
    }

    public function recent(){
        $chartUid = input('recent_id');
        return json([
            'code' => 10000,
            'message' => '操作成功',
            'data' => [],
        ]);
    }

    public function onLineUser(){
        $uid = Session::get('uid');

        $redis = Cache::store('redis')->handler();
        $onLineUserId = $redis->hkeys('uid_to_fd');
        $onLineUser = Users::whereIn('id', $onLineUserId)->where('id','<>',$uid)->select()->toArray();
        dump($onLineUser);

        return json([
            'code' => 10000,
            'message' => '操作成功',
            'data' => $onLineUser,
        ]);
    }

}
