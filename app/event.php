<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],

        'Connect'     => [
            \app\listener\RoomConnect::class
        ],
        'Chat'     => [
            \app\listener\RoomChat::class
        ],
        'Message'     => [
            \app\listener\RoomMassmess::class
        ],
        'Join'     => [
            \app\listener\RoomJoin::class
        ],
        'MassMess'     => [
            \app\listener\RoomMassmess::class
        ],
        'UserInfo'     => [
            \app\listener\UserInfo::class
        ],
        'Leave'     => [
            \app\listener\RoomLeave::class
        ],
        'Close'     => [
            \app\listener\RoomClose::class
        ]
    ],

    'subscribe' => [
    ],
];
