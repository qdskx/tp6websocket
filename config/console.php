<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'consumer' => 'app\command\Consumer',
        'direct_consumer' => 'app\command\DirectConsumer',
        'fanout_consumer' => 'app\command\FanoutConsumer',
        'topic_consumer' => 'app\command\TopicConsumer',
        'work_fair_consumer' => 'app\command\WorkFairConsumer',
        'work_lunxun_consumer' => 'app\command\WorkLunxunConsumer',
    ],
];
