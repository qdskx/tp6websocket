<?php
declare (strict_types = 1);

namespace app\skx\listener;

class RoomLeave
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event , \think\swoole\Websocket $ws , \think\swoole\websocket\Room $room)
    {

        var_dump('leave' , $event);   //离开时客户端传来的参数
        // 当前客户端id
        $fd = $ws->getSender();
        $roomId = $event['room'];
        $clients = $room->getClients($roomId);
        if(!in_array($fd, $clients)){
            return $ws->emit('leaveCallback', "{$fd}不在{$roomId}房间内~");
        }

//        对比离开前后的客户端个数
        $ws->leave($roomId);
        $clients = $room->getClients($roomId);
//        var_dump($roomId.' 房间下有'.count($clients).'个用户', $clients);



        $rooms = $room->getRooms($fd);
//        回复给客户端的消息
        $info =[];
        $info[] = $fd . '离开房间 ' . $roomId;
        $info[] = $roomId.' 房间下有'.count($clients).'个用户';
        $ws->to($roomId)->emit('leaveCallback' , $info);



//        同时离开多个房间
//        $ws->leave(['room1' , 'room2']);
//        指定客户端离开指定房间
//        $ws->setSender(3)->leave('room');






    }

}
