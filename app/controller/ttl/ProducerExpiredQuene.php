<?php
namespace app\controller\ttl;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use think\Exception;

/**
 * RabbitMQ-队列定义过期时间
 * Class ProducerExpiredQuene
 * @package app\controller\direct
 */
class ProducerExpiredQuene{

    protected $mqConfig;
    protected $mqDirectConfig;
    public function __construct(){
        $this->mqConfig = config('mq');
        $this->mqDirectConfig = $this->mqConfig['ttl_quene'];
    }

    public function index(){
        try{

            $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqDirectConfig['vhost']);
            $channel = $connection->channel();
            $channel->exchange_declare($this->mqDirectConfig['exchange_name'], AMQP_EX_TYPE_FANOUT, false, true, false);
            $args = new AMQPTable();
            $args->set('x-message-ttl', 10000);
            $channel->queue_declare($this->mqDirectConfig['quene_name'], false, true, false, false, false, $args);

            $channel->queue_bind($this->mqDirectConfig['quene_name'], $this->mqDirectConfig['exchange_name']);

            $msg = new AMQPMessage(date('Y-m-d H:i:s').' HELLO WORLD!', ['delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($msg, $this->mqDirectConfig['exchange_name']);

            echo '投递消息结束';

        }catch (Exception $exception){
            var_dump($exception->getMessage());
        }
    }
}