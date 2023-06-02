<?php
declare (strict_types = 1);

namespace app\skx\listener;

use think\facade\Cache;

class RoomJoin
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event , \think\swoole\Websocket $ws, \think\swoole\websocket\Room $room)
    {

        var_dump('join' , $event);  //加入时客户端传入的参数

        // 当前客户端id
        $fd = $ws->getSender();
        $roomId = $event['room'];
//        判断这个房间有没有自己 如果有自己就不需要再次发送通知
        $clients = $room->getClients($roomId);
        if(in_array($fd, $clients)){
            return $ws->emit('joinCallback', "{$fd}已在{$roomId}房间内~");
        }

        // 加入房间
        $ws->join($roomId);

//        当前fd加入的room
        $roomCount = $room->getRooms($fd);

        //        指定房间下的用户
        $userCount = $room->getClients($roomId);

//        $redis = Cache::store('redis')->handler();
//        $redis->select(2);

//        存储房间的用户
//        $uidJson = $redis->hget('all_room', $roomId);
//        $uidArr = json_decode($uidJson, true);
//        $redis->hset('all_room', $roomId, json_encode($uidArr));

//        存储用户加入的房间
//        $roomJson = $redis->hget('all_');



//        告诉房间某人加入的消息
        $ws->to($roomId)->emit('joinCallback' , $fd.'加入房间'.$roomId);


    }    
}
