<?php
//rabbitmq配置文件
return [
    'host'  =>  '127.0.0.1',
    'port'  =>  '5672',
    'user'  =>  'admin',
    'password'  =>  'admin',
    'fanout'    =>  [
        'vhost'             =>  'fanout_demo',
        'exchange_name'     =>  'fanout_exchange',
        'quene_name'        =>  'fanout_quene',
        'consumer_tag'      =>  'fanout_tag',
    ],
    'direct'    =>  [
        'vhost'             =>  'direct_demo',
        'exchange_name'     =>  'direct_exchange',
        'quene_name'        =>  'direct_quene',
        'consumer_tag'      =>  'direct_tag',
    ],
    'topic'    =>  [
        'vhost'             =>  'topic_demo',
        'exchange_name'     =>  'topic_exchange',
        'quene_name'        =>  'topic_quene',
        'consumer_tag'      =>  'topic_tag',
    ],
    'work'    =>  [
        'fair'  =>  [
            'vhost'             =>  'work_fair_demo',
            'exchange_name'     =>  'work_fair_exchange',
            'quene_name'        =>  'work_fair_quene',
            'consumer_tag'      =>  'work_fair_tag',
        ],
        'lunxun'  =>  [
            'vhost'             =>  'work_lunxun_demo',
            'exchange_name'     =>  'work_lunxun_exchange',
            'quene_name'        =>  'work_lunxun_quene',
            'consumer_tag'      =>  'work_lunxun_tag',
        ],
    ],
    'ttl_msg'    =>  [
        'vhost'             =>  'ttl_demo',
        'exchange_name'     =>  'ttl_exchange',
        'quene_name'        =>  'ttl_quene',
        'consumer_tag'      =>  'ttl_tag',
    ],
    'ttl_quene'    =>  [
        'vhost'             =>  'ttl_quene_demo',
        'exchange_name'     =>  'ttl_quene_exchange',
        'quene_name'        =>  'ttl_quene_quene',
        'consumer_tag'      =>  'ttl_quene_tag',
    ],
    'dead_letter'    =>  [
        'vhost'             =>  'dead_letter_demo',
        'exchange_name'     =>  'dead_letter_normal_exchange',
        'exchange_name_dlx' =>  'dead_letter_dlx_exchange',
        'quene_name'        =>  'dead_letter_normal_quene',
        'quene_name_dlx'    =>  'dead_letter_dlx_quene',
        'consumer_tag'      =>  'dead_letter_tag',
        'routing_key'       =>  'dlx_routing',
    ],

];