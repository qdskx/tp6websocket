<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 2021/7/22
 * Time: 10:54
 */
namespace app\skx\listener;

use app\skx\model\Users;
use think\facade\Cache;


/**
 * 管理员页面
 * 所有在线用户
 * Class RoomAdmin
 * @package app\skx\listener
 */
class RoomAdmin{

    public function handle($event, \think\swoole\Websocket $ws, \think\swoole\websocket\Room $room){

        $fd = $ws->getSender();
        $redis = Cache::store('redis')->handler();


        $userData = $redis->hvals('all_fd');
        $userList = [];

        foreach ($userData as $datum){
            $value = json_decode($datum, true);
            $uid = $value['uid'];
            $name = Users::field('username')->find($uid);
            $userList[] = [
                'username'  =>  $name,
                'uid'       =>  $uid,
                'fd'        =>  $value['fd'],
            ];
        }


        $ws->to($fd)->emit('adminCallback', $userList);

    }
}
