<?php
namespace app\controller;

use PhpAmqpLib\Connection\AMQPConnectionFactory;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\Exception;

class Producer{

    protected $mqConfig;
    public function __construct(){
        $this->mqConfig = config('mq');
    }

    public function index(){
        try{

            $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqConfig['vhost']);
            $channel = $connection->channel();
            $channel->exchange_declare('direct_exchange', AMQP_EX_TYPE_DIRECT, true, true, false);
            $channel->queue_declare($this->mqConfig['quene_name'].'1', false, true, false, false);
            $channel->queue_declare($this->mqConfig['quene_name'].'2', false, true, false, false);
            $channel->queue_declare($this->mqConfig['quene_name'].'3', false, true, false, false);

            $channel->queue_bind($this->mqConfig['quene_name'].'1', 'direct_exchange', 'email');
            $channel->queue_bind($this->mqConfig['quene_name'].'2', 'direct_exchange', 'email');
            $channel->queue_bind($this->mqConfig['quene_name'].'3', 'direct_exchange', 'wx');

            $channel->set_return_listener(function ($code, $txt, $exchange, $route, $msgDetail){
                echo $code .PHP_EOL. $txt.PHP_EOL. $exchange.PHP_EOL.  $route.PHP_EOL.  $msgDetail.PHP_EOL;
            });

            for($i=1;$i<=10;$i++){
                $msg = new AMQPMessage($i.' !', ['delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT]);
                $channel->basic_publish($msg, 'direct_exchange', 'email');
            }


            echo '投递消息结束';


        }catch (Exception $exception){
            var_dump($exception->getMessage());

        }

    }
}