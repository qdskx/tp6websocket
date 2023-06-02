<?php
namespace app\controller\work\lunxun;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\Exception;

/**
 * RabbitMQ-工作:公平模式,能者多劳
 * Class Producer
 * @package app\controller\direct
 */
class Producer{

    protected $mqConfig;
    protected $mqDirectConfig;
    public function __construct(){
        $this->mqConfig = config('mq');
        $this->mqDirectConfig = $this->mqConfig['work']['lunxun'];
    }

    public function index(){
        try{

            $connection = new AMQPStreamConnection($this->mqConfig['host'], $this->mqConfig['port'], $this->mqConfig['user'], $this->mqConfig['password'], $this->mqDirectConfig['vhost']);
            $channel = $connection->channel();
            $channel->exchange_declare($this->mqDirectConfig['exchange_name'], AMQP_EX_TYPE_FANOUT, false, true, false);
            $channel->queue_declare($this->mqDirectConfig['quene_name'], false, true, false, false);

            $channel->queue_bind($this->mqDirectConfig['quene_name'], $this->mqDirectConfig['exchange_name']);

            for($i=1;$i<=20;$i++){
                $msg = new AMQPMessage($i.' HELLO WORLD!', ['delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT]);
                $channel->basic_publish($msg, $this->mqDirectConfig['exchange_name']);
            }


            echo '投递消息结束';

        }catch (Exception $exception){
            var_dump($exception->getMessage());
        }
    }
}