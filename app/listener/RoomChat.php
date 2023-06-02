<?php
declare (strict_types = 1);

namespace app\listener;

use think\facade\Cache;


class RoomChat
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event , \think\swoole\Websocket $ws, \think\swoole\websocket\Room $room)
    {
        var_dump('RoomChat', $event);
        // 当前客户端id
        $fd = $ws->getSender();
        $roomId = $event['room'];
        $clients = $room->getClients($roomId);
//        if(!in_array($fd, $clients)){
//            return $ws->emit('chatCallback', "{$fd}不在{$roomId}房间内，无法进入发布聊天~");
//        }

        Cache::store('redis')->handler();
        $ws->to($roomId)->emit('chatCallback' , [
            'from'      =>  $fd,
            'message'   =>  $fd.' 说 '.$event['mess'],
        ]);
    }
}
