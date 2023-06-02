<?php
/**
 * Created by PhpStorm.
 * User: skx
 * Date: 2021/6/15
 * Time: 15:53
 */
namespace app\listener;

use think\facade\Cache;
use think\facade\Log;


class RoomClose
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event , \think\swoole\Websocket $ws, \think\swoole\websocket\room $room)
    {
        // $event 啥意思???????
//        var_dump($event);
        $fd = $ws->getSender();

        var_dump('close', $fd.PHP_EOL);
//        Log::info(print_r($ws, 1));
        $data = [
            'mess' =>   $fd.'断开连接',
            'user' =>   $fd,
        ];
//        $redis = Cache::store('redis')->handler();
//        $redis->hdel('all_fd', $fd);

        $redis = Cache::store('redis')->handler();
        $leaveUid = $redis->hget('fd_to_uid', $fd);
        $redis->hdel('fd_to_uid', $fd);
        $redis->hdel('uid_to_fd', $leaveUid);


//        $ws->broadcast()->emit('close', $fd.'断开连接');
        $ws->broadcast()->emit('connectCallback', $data);
        // 用户关闭浏览器时，这个用户进入过的所有房间会全部退出
    }
}




/**
 * 11 加入了123房间  1
 * 12 加入了123房间  2
 * 13 加入了123房间  3
 * 14 加入了123房间  4
 * 15              5
 * 16              1
 * 17              1
 * 18              1
 * 18              2
 * 20              1
 * 21              2
 * 23              4
 * 22              3
 *
 *
 *
 *
 */