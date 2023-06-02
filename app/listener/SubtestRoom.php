<?php
/**
 * Created by PhpStorm.
 * User: skx
 * Date: 2021/6/17
 * Time: 18:15
 */
namespace app\skx\listener;

use think\swoole\websocket;

class SubtestRoom{

    public $websocket   = null;
    public $fd          = '';
    public $roomId      = '';

    public function __construct(websocket $websocket)
    {
        $this->websocket = $websocket;
    }

    public function onConnect($event){
        var_dump('onConnect', $event);
        $this->fd = $this->websocket->getSender();
        var_dump($this->websocket->getSender());
        $this->websocket->emit('send_fd' , '已连接');
    }

    public function onJoin($event){
        var_dump('onJoin', $event);
        $this->roomId = $event['room'];
        $info = [];
        $info[] = $this->fd . '加入房间 '.$this->roomId;
        $this->websocket->to($this->roomId)->emit('joinCallback' , $info);
    }

    public function onLeave($event){
        var_dump('onLeave', $event);
        $info =[];
        $info[] = $this->fd . '离开房间 ' . $this->roomId;
        $this->websocket->to($this->roomId)->emit('leaveCallback' , $info);
    }

    public function onChat($event){
        var_dump('onChat', $event);
        $this->websocket->to($this->roomId)->emit('chatCallback' , [
            'from'      =>  $this->fd,
            'message'   =>  $this->fd.' 说 '.$event['mess'],
        ]);
    }

    public function onClose($event){
        var_dump('onClose', $event);
        $this->websocket->broadcast()->emit('close', $this->fd.'离开房间');
    }
}
