<?php
namespace app\controller\work\lunxun;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer{

    protected $mqConfig;
    protected $mqDirectConfig;

    public function __construct(){
        $this->mqConfig = config('mq');
        $this->mqDirectConfig = $this->mqConfig['work']['lunxun'];
    }

    public function index($sleepNum){
        $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqDirectConfig['vhost']);
        $channel = $connection->channel();

        $callback = function ($msg) use ($sleepNum){
            echo '接受到的消息内容为:'.$msg->body.PHP_EOL;
            $msg->ack();
            sleep($sleepNum);
        };
        $channel->basic_qos(null, null, false);
        $channel->basic_consume($this->mqDirectConfig['quene_name'], '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}

