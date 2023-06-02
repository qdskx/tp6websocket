<?php
namespace app\model;

use think\Model;

class BaseEcshop extends Model{

    const DB_CONNECTION = 'ecshop';

    public function __construct(array $data = [])
    {
        $this->setConnection(self::DB_CONNECTION);
        parent::__construct($data);
    }
}