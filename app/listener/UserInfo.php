<?php
/**
 * Created by PhpStorm.
 * User: skxs
 * Date: 2021/7/22
 * Time: 18:20
 */
declare (strict_types = 1);

namespace app\skx\listener;

use think\facade\Cache;

class UserInfo
{
    /**
     * 连接后发生以后信息的事件监听处理
     *
     * @return mixed
     */
    public function handle($event , \think\swoole\Websocket $ws)
    {
        $fd = $ws->getSender();
        $uid = $event['uid'];

        $redis = Cache::store('redis')->handler();
        $redis->select(2);
        $userInfo = [
            'in_time'   =>  time(),
            'uid'   =>  $uid,
            'fd'    =>  $fd,
        ];

        $redis->hset('all_fd', $fd, json_encode($userInfo));

        $ws->emit('userinfoCallback', '接收用户信息成功');
    }
}