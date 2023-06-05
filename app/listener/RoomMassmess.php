<?php
declare (strict_types = 1);

namespace app\listener;

use app\Model\Chat;
use app\Model\Users;
use think\facade\Cache;

/**
 * 管理员发送消息
 * 可以到所有用户，也可以到指定用户
 * Class RoomMassmess
 * @package app\listener
 */
class RoomMassmess
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event , \think\swoole\Websocket $ws)
    {

        // send_to不传表示发送到所有用户，否则发送到指定用户或房间
//        $sendTo = $event['send_to'] ?? 0;
        $mess = $event['mess'] ?? '';
        $fromUid = $event['from_user'] ?? 0;
        $recvUid = $event['recv_user'] ?? 0;
        $fd = $ws->getSender();

        $data = [
            'msg' =>   $mess,
            'from_id' =>   $fromUid,
            'recv_id' =>   $recvUid,
            'created' =>   date('Y-m-d H:i:s'),
        ];

        $redis = Cache::store('redis')->handler();
        $recvFd = $redis->hget('uid_to_fd', $recvUid);
        $fromFd = $redis->hget('uid_to_fd', $fromUid);
        if(!$fromFd || !$recvFd)return;
        Chat::create($data);


        if($fd == $recvFd)$retFd = $recvUid;
        else if($fd == $fromFd)$retFd = $fromUid;

        var_dump('retuid', $retFd, 'fd', $fd, 'recv_uid', $recvUid);
        $fromUserInfo['info'] = Users::find($retFd);
        $fromUserInfo['msg'] = $mess;
        $fromUserInfo['from'] = $fromUid;


        $ws->to($recvFd)->emit('messageCallback', $fromUserInfo);
//        $ws->broadcast()->emit('messageCallback', $data);  // 自己收不到



//        指定客户端接入某个指定房间
//        $ws->setSender(3)->join($event['room']);
//        var_dump('RoomMassmess', $event);
        // 群发到所有连接的用户，自己收不到，其他的房间也能收到消息
//        $ws->broadcast()->emit('massmessCallback', $ws->getSender().' 说'.$event['mess']);
        // 群发到房间内的所有人，包括自己，其他房间收不到消息
//        $ws->broadcast()->to($event['room'])->emit('massmessCallback', $ws->getSender().' 说 '.$event['mess']);
    }

}
