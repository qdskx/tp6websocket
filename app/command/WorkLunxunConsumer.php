<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class WorkLunxunConsumer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('work_lunxun_consumer')
            ->addArgument('sleep_num', Argument::REQUIRED, "睡多久")
            ->setDescription('消费者');
    }

    protected function execute(Input $input, Output $output)
    {
        (new \app\controller\work\lunxun\Consumer())->index($input->getArgument('sleep_num'));
    }
}
