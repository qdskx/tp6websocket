<?php
/**
 * Created by PhpStorm.
 * User: skx
 * Date: 2021/6/15
 * Time: 15:53
 */
namespace app\listener;
use app\Model\Users;
use think\facade\Cache;
use think\facade\Session;

class RoomConnect
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event , \think\swoole\Websocket $ws)
    {

        $currentUid = input('uid');

        $fd = $ws->getSender();
        var_dump('connect', $fd.PHP_EOL);

        $data = [
            'mess' =>   $fd.'已连接',
            'user' =>   $fd,
        ];

        $redis = Cache::store('redis')->handler();

        $currentUserInfo = Users::find($currentUid);


        $redis->hset('uid_to_fd', $currentUid, $fd);
        $redis->hset('fd_to_uid', $fd, $currentUid);







        $ws->broadcast()->emit('connectCallback', $currentUserInfo);
//        $ws->broadcast()->emit('messageCallback', $data);


    }
}
