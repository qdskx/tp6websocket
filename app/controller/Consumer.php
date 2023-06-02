<?php
namespace app\controller;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer{

    protected $mqConfig;
    protected $channel;
    public function __construct(){
        $this->mqConfig = config('mq');
    }

    public function index($queneName, $sleepSecond){
        $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqConfig['vhost']);
        $this->channel = $connection->channel();

        $callback = function ($msg) use ($sleepSecond) {
            //写入数据库
            //todo
            echo $msg->body . "\n";
            //确认消息已被消费，从生产队列中移除
            $msg->ack();
//            $msg->nack(true);
            sleep($sleepSecond);
        };

        echo '开始消费消息'.PHP_EOL;
//        prefetch_size
//        最大unacked消息的字节数；
//        prefetch_count
//        最大unacked消息的条数；
//        global：
//        上述限制的限定对象，false=限制单个消费者；true=限制整个信道
//
//        设置每个consumer的prefetch count：
//        global指定false，即可
//        $this->channel->basic_qos(null, 1, false);
        $this->channel->basic_consume($queneName, $this->mqConfig['consumer_tag'], false, false, false, false, $callback);

        while ($this->channel->is_open()) {
            $this->channel->wait();
        }

        $this->channel->close();
        $connection->close();

    }


}

//(new Consumer())->index();