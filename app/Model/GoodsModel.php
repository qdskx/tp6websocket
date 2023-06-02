<?php


namespace app\model;

use Elastic\Elasticsearch\ClientBuilder;


class GoodsModel extends BaseEcshop
{
    protected $table = 'ecs_goods';
    protected $key = 'goods_id';
    protected $pk = 'goods_id';

    public $esClient;

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->esClient = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
//        $this->getIndex();
    }

    public function highLightSearch(){
        $param['index'] = $this->indexDb;
        $query = [
            'query' =>  [
                'term'  =>  [
                    'color' =>  'red',
                ],
            ],
            'highlight' =>  [
                'pre_tags'  =>  "<span style='color:yellow'>",
                'post_tags' =>  "</span>",
                'fields'    =>  [
                    'color' =>  new \stdClass(),        // 这里必须是一个空对象
                ],
            ],
        ];
        $param['body'] = $query;
        dump($param);
        return $this->esClient->search($param);
    }

    protected function getIndex(){
        $this->indexDb = input('db_name', 'cars');
    }


}