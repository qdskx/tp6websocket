<?php
namespace app\controller;

use app\BaseController;
use app\Model\Chat;
use think\facade\Cache;
use think\facade\Session;
use think\facade\View;
use app\Model\Users;

class Index extends BaseController
{
    public function index(){
        $uid = Session::get('uid');
        if(!$uid){
            return View::fetch('/login');
        }

        //TODO 在线用户列表
        $redis = Cache::store('redis')->handler();
        $onLineUserId = $redis->hkeys('uid_to_fd');
        $onLineUser = Users::whereIn('id', $onLineUserId)->where('id','<>',$uid)->select()->toArray();

        //TODO 与某一用户的最近聊天记录


        //TODO 当前用户信息
        $userinfo = Users::find($uid);
        return View::fetch('/index', ['userInfo'=>$userinfo, 'onLine'=>$onLineUser]);
    }

    /**
     * 两人最近的聊天记录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function recent(){
        $chartUid = input('recent_id');
        $data = Chat::whereIn('from_id', [Session::get('uid'),$chartUid])
            ->whereIn('recv_id', [Session::get('uid'),$chartUid])
            ->order('id asc')->select()->toArray();
        $dataAnd = Users::find($chartUid);
        $ret['uname'] = $dataAnd['user_name'] ?? '';
        $ret['data'] = $data;
        return json([
            'code' => 10000,
            'message' => '操作成功',
            'data' => $ret,
        ]);
    }

}
