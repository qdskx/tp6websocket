<?php
namespace app\controller\es\goods;

use app\model\GoodsModel;
use Elastic\Elasticsearch;
use Elastic\Elasticsearch\ClientBuilder;
use think\facade\Db;
use think\facade\View;

class Index{

    public $esObj;

    public function __construct(){
        $this->esObj = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
    }


    public function save(){
        $data = GoodsModel::alias('T1')
            ->join('ecs_category T2', 'T1.cat_id=T2.cat_id')
            ->join('ecs_brand T3', 'T1.brand_id=T3.brand_id')
            ->field('T1.goods_id,T1.goods_name,T1.market_price,T2.cat_name,T3.brand_name')
            ->select()->toArray();
        $add = [];
        foreach ($data as $datum){
            $add[]['index']['_index'] = 'goods';
            $add[] = [
                'id'            =>  $datum['goods_id'],
                'goods_name'    =>  $datum['goods_name'],
                'cat_name'      =>  $datum['cat_name'],
                'brand_name'    =>  $datum['brand_name'],
                'market_price'  =>  $datum['market_price'],
            ];
        }
        $bulkData['body'] = $add;
        $res = $this->esObj->bulk($bulkData);
        halt($res,$res->asArray());

    }

    public function index(){
//        $data = GoodsModel::paginate(4);
//        halt($data->render());
        $page = input('page', 1);
        $from = ($page-1)*50;
        $param['index'] = 'goods';
        $queryParam = input('query', '');
        if($queryParam == ''){
            $query = [
                'query' =>  [
                    'match_all' =>  [
                        '_name'=>'match_all'
                    ],
                ],
                'sort'  =>  [
                    'id'    =>  [
                        'order' =>  'desc'
                    ],
                ],
                'from'  =>  $from,
                'size'  =>  50,
            ];
        }else{
            $query = [
                'query' =>  [
                    'multi_match'  =>  [
                        'query' =>  $queryParam,
                        'fields' =>  ['goods_name'],
//                    'fields' =>  ['goods_name','cat_name','brand_name'],
                ],
                ],
                'highlight' =>  [
                    "pre_tags" => "<em style='color: red'>",
                    "post_tags" => "</em>",
                    'fields'    =>  [
                        'goods_name' =>  new \stdClass(),     // 这里必须是一个空对象
                    ],
                ],
                'from'  =>  $from,
                'size'  =>  50,
            ];
        }
        $param['body'] = $query;
        $data =  $this->esObj->search($param);
//        $retData = $data->asArray();
        $total = $data['hits'];
        $ret = $total['hits'];


        if($queryParam != ''){
            foreach ($ret as $key => $value){
                $ret[$key]['_source']['goods_name'] = $value['highlight']['goods_name'][0];
//            $ret[$key]['_source']['cat_name'] = $value['highlight']['cat_name'][0];
            }
        }

        $rets = array_column($ret, '_source');
        $page = [
            'page'  =>  $page,
            'last'  =>  intval(ceil($total['total']['value']/50)),
        ];
        return View::fetch('/goods/index', ['data'=>$rets, 'page_data'=>$page]);
    }

}