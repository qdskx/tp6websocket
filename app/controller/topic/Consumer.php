<?php
namespace app\controller\topic;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer{

    protected $mqConfig;
    protected $mqDirectConfig;

    public function __construct(){
        $this->mqConfig = config('mq');
        $this->mqDirectConfig = $this->mqConfig['topic'];
    }

    public function index($queneName){
        $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqDirectConfig['vhost']);
        $channel = $connection->channel();

        $callback = function ($msg){
            echo '接受到的消息内容为:'.$msg->body.PHP_EOL;
            $msg->ack();
        };
        $channel->basic_consume($queneName, '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}

