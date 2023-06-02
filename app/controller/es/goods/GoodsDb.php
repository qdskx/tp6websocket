<?php
namespace app\controller\es\goods;

use Elastic\Elasticsearch;
use Elastic\Elasticsearch\ClientBuilder;
use think\facade\Db;

class GoodsDb{

    public $esObj;

    public function __construct(){
        $this->esObj = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
    }

    public function save(){
        $databaseInfo = [
            'index' =>  input('db_name', ''),
            'body'  =>  [
                'mappings'  =>  [
                    'properties'    =>  [
                        'id' =>  [
                            'type'  =>  'long'
                        ],
                        'goods_name' =>  [
                            'type'  =>  'text'
                        ],
                        'category' =>  [
                            'type'  =>  'text'
                        ],
                        'brand' =>  [
                            'type'  =>  'keyword'
                        ],
                        'market_price' =>  [
                            'type'  =>  'float'
                        ],
                    ]
                ]
            ]
        ];

        $res = $this->esObj->indices()->create($databaseInfo);
        halt($res,$res->asArray());
    }

    public function read($id){
        $param = [
            'index' =>  $id,
        ];
        $res = $this->esObj->indices()->get($param);    // 获取数据库所有信息
//        $res = $this->esObj->indices()->getMapping($param); // 只获取数据字段信息
//        $res = $this->esObj->indices()->getSettings($param); // 只获取数据分片信息
        halt(1,$res->asArray());
    }





}