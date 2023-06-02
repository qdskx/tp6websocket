<?php
namespace app\controller;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer3{

    protected $mqConfig;
    protected $channel;
    public function __construct(){
        $this->mqConfig = config('mq');
    }

    public function index(){
        $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqConfig['vhost']);
        $this->channel = $connection->channel();

        echo '开始消费消息'.PHP_EOL;
        $this->channel->basic_consume($this->mqConfig['quene_name'].'3', $this->mqConfig['consumer_tag'], false, false, false, false, 'callback');


    }

    public function callback($msg, $quene){
        var_dump(11,$msg->getBody());
        $this->channel->basic_ack($msg->getDeliveryTag());
    }
}

//(new Consumer3())->index();