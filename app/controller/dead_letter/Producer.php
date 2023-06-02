<?php
namespace app\controller\dead_letter;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use think\Exception;

/**
 * RabbitMQ-死信队列，可以是任何模式，路由、订阅都行
 * Class Producer
 * @package app\controller\direct
 */
class Producer{

    protected $mqConfig;
    protected $mqDirectConfig;
    public function __construct(){
        $this->mqConfig = config('mq');
        $this->mqDirectConfig = $this->mqConfig['dead_letter'];
    }

    public function index(){
        try{

            $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqDirectConfig['vhost']);
            $channel = $connection->channel();
            $channel->exchange_declare($this->mqDirectConfig['exchange_name'], AMQP_EX_TYPE_FANOUT, false, true, false);
            $channel->exchange_declare($this->mqDirectConfig['exchange_name_dlx'], AMQP_EX_TYPE_FANOUT, false, true, false);

            $args = new AMQPTable();
            $args->set('x-message-ttl', 10000);
            $args->set('x-dead-letter-exchange', $this->mqDirectConfig['exchange_name_dlx']);
            $args->set('x-dead-letter-routing-key', $this->mqDirectConfig['routing_key']);

            $channel->queue_declare($this->mqDirectConfig['quene_name'], false, true, false, false, false, $args);
            $channel->queue_declare($this->mqDirectConfig['quene_name_dlx'].'1', false, true, false, false);
            $channel->queue_declare($this->mqDirectConfig['quene_name_dlx'].'2', false, true, false, false);

            $channel->queue_bind($this->mqDirectConfig['quene_name'], $this->mqDirectConfig['exchange_name'] );
//            $channel->queue_bind($this->mqDirectConfig['quene_name_dlx'], $this->mqDirectConfig['exchange_name_dlx'], $this->mqDirectConfig['routing_key'] );
            $channel->queue_bind($this->mqDirectConfig['quene_name_dlx'].'1', $this->mqDirectConfig['exchange_name_dlx']);
            $channel->queue_bind($this->mqDirectConfig['quene_name_dlx'].'2', $this->mqDirectConfig['exchange_name_dlx']);

            $msg = new AMQPMessage(date('Y-m-d H:i:s').' HELLO WORLD!', ['delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($msg, $this->mqDirectConfig['exchange_name']);

            echo '投递消息结束';

        }catch (Exception $exception){
            var_dump($exception->getMessage());
        }
    }
}