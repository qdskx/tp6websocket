<?php
namespace app\controller;

use think\facade\Session;
use think\facade\View;
use app\Model\Users;
use app\Model\LoginLog;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Login{

    public function index(){
        return View::fetch('/login');
    }

    public function doLogin(){
        $username = input('user_name');

        $exists = Users::where(['user_name'=>$username])->find();
        if($exists){
            $uid = $exists['id'];
        }else{
            $addRes = Users::create(['user_name'=>$username]);
            $uid = $addRes->id;
        }
        Session::set('uid', $uid);
//        $this->sendMsg();
        return json([
            'code' => 10000,
            'message' => '操作成功',
            'data' => $uid,
        ]);
    }

    public function sendMsg()
    {
        $connectionConfig = config('mq');
        $connection = new AMQPStreamConnection($connectionConfig['host'], $connectionConfig['port'], $connectionConfig['user'], $connectionConfig['password'], $connectionConfig['vhost']);
        $channel = $connection->channel();

        $channel->queue_declare($connectionConfig['quene_name'], false, true, false, false);

        $data = 'root login success-'.time();

        $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
        $channel->basic_publish($msg, $exchange = '', $connectionConfig['quene_name']);

        $channel->close();
        $connection->close();
    }
}