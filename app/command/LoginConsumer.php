<?php
declare (strict_types = 1);

namespace app\command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class LoginConsumer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('login_consumer')
            ->setDescription('the app\command\loginconsumer command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('app\command\loginconsumer');

        $connectionConfig = config('mq');
        $connection = new AMQPStreamConnection($connectionConfig['host'],$connectionConfig['port'],$connectionConfig['user'], $connectionConfig['password'], $connectionConfig['vhost']);

        $channel = $connection->channel();
        $channel->queue_declare($connectionConfig['quene_name'], false, true, false, false);

        $callback = function ($msg) use ($output) {
            $output->writeln($msg->body);
            //写入数据库
            //todo
            echo 'received = ', $msg->body . "\n";
            //确认消息已被消费，从生产队列中移除
            $msg->ack();
        };

        //设置消费成功后才能继续进行下一个消费
        $channel->basic_qos(null, 1, null);

        //开启消费no_ack=false,设置为手动应答
        $channel->basic_consume($connectionConfig['quene_name'], $connectionConfig['consumer_tag'], false, false, false, false, $callback);


        //不断的循环进行消费
        while ($channel->is_open()) {
            $channel->wait();
        }

        //关闭连接
        $channel->close();
        $connection->close();
    }
}
