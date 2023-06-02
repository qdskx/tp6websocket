<?php
namespace app\controller\direct;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\Exception;

/**
 * RabbitMQ-路由模式
 * Class Producer
 * @package app\controller\direct
 */
class Producer{

    protected $mqConfig;
    protected $mqDirectConfig;
    public function __construct(){
        $this->mqConfig = config('mq');
        $this->mqDirectConfig = $this->mqConfig['direct'];
    }

    public function index(){
        try{

            $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqDirectConfig['vhost']);
            $channel = $connection->channel();
            $channel->exchange_declare($this->mqDirectConfig['exchange_name'], AMQP_EX_TYPE_DIRECT, false, true, false);
            $channel->queue_declare($this->mqDirectConfig['quene_name'].'1', false, true, false, false);
            $channel->queue_declare($this->mqDirectConfig['quene_name'].'2', false, true, false, false);
            $channel->queue_declare($this->mqDirectConfig['quene_name'].'3', false, true, false, false);

            $channel->queue_bind($this->mqDirectConfig['quene_name'].'1', $this->mqDirectConfig['exchange_name'], 'email');
            $channel->queue_bind($this->mqDirectConfig['quene_name'].'2', $this->mqDirectConfig['exchange_name'], 'email');
            $channel->queue_bind($this->mqDirectConfig['quene_name'].'3', $this->mqDirectConfig['exchange_name'], 'wx');

            $msg = new AMQPMessage(date('Y-m-d H:i:s').' HELLO WORLD!', ['delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($msg, $this->mqDirectConfig['exchange_name'], 'email');

            echo '投递消息结束';

        }catch (Exception $exception){
            var_dump($exception->getMessage());
        }
    }
}