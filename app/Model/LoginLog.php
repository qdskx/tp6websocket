<?php
namespace app\Model;

use think\Model;

class LoginLog extends Model{
    public $table = 'login_log';

    public $field = [
        'id',
        'login_name',
        'login_ip',
        'browser',
        'os',
        'login_at',
        'status',
    ];

}