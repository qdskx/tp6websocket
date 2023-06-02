<?php
namespace app\controller\es;

use Elastic\Elasticsearch;
use Elastic\Elasticsearch\ClientBuilder;

class EsDb{

    public $esObj;

    public function __construct(){
        $this->esObj = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
    }

    /**
     * 获取一个数据库的明细
     * 不存在会报错404
     * @param string $id    数据库名称
     * @throws Elasticsearch\Exception\ClientResponseException
     * @throws Elasticsearch\Exception\MissingParameterException
     * @throws Elasticsearch\Exception\ServerResponseException
     */
    public function read($id = ''){
        $param = [
            'index' =>  $id,
        ];
        $res = $this->esObj->indices()->get($param);    // 获取数据库所有信息
//        $res = $this->esObj->indices()->getMapping($param); // 只获取数据字段信息
//        $res = $this->esObj->indices()->getSettings($param); // 只获取数据分片信息
        halt(1,$res->asArray());
    }

    public function save(){
        $param = [
            'index' =>  input('db_name', ''),
            'body' =>  [
                'mappings'  =>  [
                    'properties'    =>  [
                        'user_name' =>  [
                            'type'  =>  'keyword'
                        ]
                    ]
                ]
            ],
        ];
        dump($param);
        $res = $this->esObj->indices()->create($param);
        halt($res->asArray());
    }

    /**
     * 删除数据库
     * @param string $id    数据库名称
     * @throws Elasticsearch\Exception\ClientResponseException
     * @throws Elasticsearch\Exception\MissingParameterException
     * @throws Elasticsearch\Exception\ServerResponseException
     */
    public function delete($id = ''){
        $param = [
            'index' =>  $id,
        ];
        $res = $this->esObj->indices()->delete($param);
        halt($res->asArray());
    }
}