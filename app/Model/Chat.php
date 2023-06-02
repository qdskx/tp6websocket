<?php
namespace app\Model;

use think\Model;

class Chat extends Model{
    public $table = 'chat';

    public $field = [
        'id',
        'from_id',
        'msg',
        'recv_id',
        'created',
    ];

}