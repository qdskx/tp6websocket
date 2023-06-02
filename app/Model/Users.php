<?php
namespace app\Model;

use think\Model;

class Users extends Model{

    public $table = 'users';

    public $field = [
        'id',
        'user_name',
        'password',
        'created_at',
        'updated_at',
    ];

}