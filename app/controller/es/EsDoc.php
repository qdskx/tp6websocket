<?php
namespace app\controller\es;

use app\Model\Users;
use Elastic\Elasticsearch;
use Elastic\Elasticsearch\ClientBuilder;

class EsDoc{

    public $esObj;
    public $indexDb;

    public function __construct(){
        $this->esObj = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
        $this->getIndex();
    }

    public function index(){
//        $res = $this->querySearch();
//        $res = $this->boolQuerySearch();
//        $res = $this->aggsSearch();
//        $res = $this->highLightSearch();
//        $res = $this->rangeSearch();
        $res = $this->fuzzySearch();
        halt($res->asArray());
    }

    public function read(string $id){
        $param['index'] = 'student';
        $param['type'] = '_doc';
        $param['id'] = $id;
        $isExists = $this->esObj->exists($param)->asBool();
        if(!$isExists)halt('数据不存在');
        $data = $this->esObj->get($param);
        halt($data,$data->asArray());
    }

    public function delete($id){
        $param['index'] = $this->indexDb;
        $param['type'] = '_doc';
        $param['id'] = $id;
        $isExists = $this->esObj->exists($param)->asBool();
        if(!$isExists)halt('数据不存在');
        $data = $this->esObj->delete($param);
        halt($data,$data->asArray());
    }

    protected function boolQuerySearch(){
        $param['index'] = $this->indexDb;
        $param['body']['query']['bool'] = [
            'must'  =>  [
                'terms'  =>  [
                    'color'=>['red', 'green'],
                    '_name'=>'color',
                ],
//                'match'  =>  [
//                    'color' =>  [
//                        'query' =>  'red',
//                        '_name' =>  'color',
//                    ],
//                ],
            ],
            'must_not'  =>  [
                'term' =>  [
                    'color' =>  [
                        'value' =>  'yellow',
                    ],
                ],
            ],
            'should'    =>  [
                'terms' =>  [
                    'make'  =>  ['honda'],
                    '_name' =>  'make',
                ],
            ],
        ];
        dump($param);
        return $this->esObj->search($param);
    }

    protected function aggsSearch(){
        $searchType = input('search_type', 'max');
        $param['index'] = $this->indexDb;
        $param['size'] = 0;
        switch($searchType){
            case 'max':
                $body = $this->queryAggsMax();
                break;
            case 'stats':
                $body = $this->queryAggStats();
                break;
            case 'avg':
                $body = $this->queryAggsAvg();
                break;
            case 'min':
                $body = $this->queryAggsMin();
                break;
            case 'sum':
                $body = $this->queryAggSum();
                break;
            case 'count':
                $body = $this->queryAggsValueCount();
                break;
            case 'top':
                $body = $this->queryAggsTop();
                break;
            case 'percentiles':
                $body = $this->queryAggsPercentiles();
                break;
            case 'fix':
                $body = $this->queryAggsFix();
                break;
        }
        $param['body']['aggs'] = $body;
        dump($param);
        return $this->esObj->search($param);
    }

    protected function queryAggsMax(){
        $aggs['max_price']['max']['field'] = 'price';
        return $aggs;
    }
    protected function queryAggsAvg(){
        $aggs['avg_price']['avg']['field'] = 'price';
        return $aggs;
    }
    protected function queryAggsMin(){
        $aggs['min_price']['min']['field'] = 'price';
        return $aggs;
    }
    protected function queryAggSum(){
        $aggs['sum_price']['sum']['field'] = 'price';
        return $aggs;
    }
    protected function queryAggsValueCount(){
        $aggs['count']['value_count']['field'] = 'price';
        return $aggs;
    }
    protected function queryAggsTop(){
        $aggs['top']['top_hits'] = [
            'size'  =>  2,
            'sort'  =>  [
                'price' =>  [
                    'order' =>  'desc'
                ],
            ],
        ];
        return $aggs;
    }
    protected function queryAggStats(){
        $aggs['stats_price']['stats']['field'] = 'price';
        return $aggs;
    }
    protected function queryAggsPercentiles(){
        $aggs['percentiles']['percentiles']['field'] = 'price';
        return $aggs;
    }
    /**
     * 聚合再聚合
     * @return array
     */
    protected function queryAggsFix(){
        $aggs['fix'] = [
            'terms'  =>  [
                'field' =>  'make',
            ],
            'aggs'  =>  [
                'color_group'   =>  [
                    'terms'  =>  [
                        'field' =>  'color'
                    ],
                ],
            ],
        ];
        return $aggs;
    }


    protected function querySearch(){
        $searchType = input('search_type', 'match');
        $param['index'] = $this->indexDb;
//        $param['_source'] = ['name','tags','color'];
        switch($searchType){
            case 'match':
                $body = $this->queryMatch();
                break;
            case "match_all":
                $body = $this->queryMatchAll();
//                $param['size'] = 2;       // 方式1
                $param['body']['size'] = 4; // 方式2
                $param['body']['sort']['price']['order'] = 'desc';
                break;
            case 'match_phrase':
                $body = $this->queryMatchPhrase();
                break;
            case "multi_match":
                $body = $this->queryMultiMatch();
                break;
            case "term":
                $body = $this->queryTerm();
                break;
            case "terms":
                $body = $this->queryTerms();
                $param['body']['sort']['price']['order'] = 'desc';
                break;
            case "constant_score":
                $body = $this->queryConstantScore();
                break;
        }
        $param['body']['query'] = $body;
        dump($param);
        return $this->esObj->search($param);
    }

    protected function queryMatch(){
        $field = input('field', 'name');
        $val = input('val', '');
//        $query['match'][$field] = $val;   // 方式1
        $query['match'][$field] = [         // 方式2
            'query' =>  $val,
//            'operator' =>  'and',
            'minimum_should_match' =>  '70%',   // 假设搜索张国立，70%的意思是：3(张国立是3个汉字)*70%=2.1，也就是必须包含(张国立)2个汉字
//            'minimum_should_match' =>  '60%', // 假设搜索张国立，60%的意思是：3(张国立是3个汉字)*60%=1.8，也就是必须包含(张国立)1个汉字
        ];
        return $query;
    }

    /**
     * 匹配的值当成一个整体单词(不分词)进行检索
     * @return array
     */
    protected function queryMatchPhrase(){
        $field = input('field', 'name');
        $val = input('val', '');
        $query['match_phrase'][$field] = $val;
        return $query;
    }

    protected function queryMatchAll(){
        return ['match_all'=>[
            '_name'=>'match_all' // 非得加个这才不报错
        ]];
    }

    /**
     * 在多个字段中查询
     * @return array[]
     */
    protected function queryMultiMatch(){
        $val = input('val', '');
        return ['multi_match'=>[
            'query' =>  $val,
            'fields' =>  ['name','tags'],
        ]];
    }

    /**
     * 用于精确值 匹配
     * 这些精确值可能是数字、时间、布尔或者那些未分词的字符串，
     * text类型的查不到
     * @return array[]
     */
    protected function queryTerm(){
        $field = input('field', 'name');
        $val = input('val', []);//得是数组
        return ['term'=>[
            $field => $val,
        ]];
    }

    /**
     * 指定多值进行匹配
     * 如果这个字段包含了指定值中的任何一个值，那么这个文档满足条件
     * @return array[]
     */
    protected function queryTerms(){
        $field = input('field', 'name');
        $val = input('val', []);
        $query['terms'][$field] = $val;
//        $query['terms'] = [       // 不支持
//            'field' =>  $field,
//            'query'   =>  $val
//        ];
        return $query;
    }

    /**
     * 非评分模式
     * filter：过滤器，不会计算相关度，速度快
     * @return array
     */
    protected function queryConstantScore(){
        $field = input('field', 'name');
        $val = input('val', '');
        $query['constant_score']['filter']['term'] = [
            $field  =>  $val,
        ];
        return $query;
    }

    protected function highLightSearch(){
        $param['index'] = $this->indexDb;
        $query = [
            'query' =>  [
                'term'  =>  [
                    'color' =>  'red',
                ],
            ],
            'highlight' =>  [
                'pre_tags'  =>  "<span style='color:yellow'>",
                'post_tags'  =>  "</span>",
                'fields'    =>  [
                    'color' =>  new \stdClass(),        // 这里必须是一个空对象
                ],
            ],
        ];
        $param['body'] = $query;
        dump($param);
        return $this->esObj->search($param);
    }

    /**
     * 范围查询
     * @return Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
     * @throws Elasticsearch\Exception\ClientResponseException
     * @throws Elasticsearch\Exception\ServerResponseException
     */
    protected function rangeSearch(){
        $param['index'] = $this->indexDb;
        $query = [
            'query' =>  [
                'range'  =>  [
                    'price' =>  [
                        'gte'   =>  20000,
                        'lte'   =>  80000,
                    ],
                ],
            ],
            'sort'  =>  [
                'price' =>  [
                    'order' =>  'desc'
                ],
            ],
        ];
        $param['body'] = $query;
        dump($param);
        return $this->esObj->search($param);
    }

    /**
     * 模糊查询
     * term 查询的模糊等价。它允许用户搜索词条与实际词条的拼写出现偏差，但是偏差的编辑距离不得超过2
     * @return Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
     * @throws Elasticsearch\Exception\ClientResponseException
     * @throws Elasticsearch\Exception\ServerResponseException
     */
    protected function fuzzySearch(){
        $param['index'] = $this->indexDb;
//        $query = [
//            'query' =>  [
//                'fuzzy'  =>  [
//                    'make' =>  'ddnda'
//                ],
//            ]
//        ];
        $query = [
            'query' =>  [
                'fuzzy'  =>  [
                    'name' =>  [
                        'value' =>  '高圆与',  // 查不到高圆圆
                        'fuzziness' =>  1,      // 只允许0、1、2、auto，0-完全匹配，1-允许一个字母的误差，2-允许2个
//                        "prefix_length"=> 3,    // 指定前面几个字符是不允许出现错误的
                    ],
                ],
            ]
        ];
//        $query = [
//            'query' =>  [
//                'match'  =>  [
//                    'name' =>  [
//                        'query' =>  '高圆与',
////                        'fuzziness' =>  1,      // 只允许0、1、2、auto，0-完全匹配，1-允许一个字母的误差，2-允许2个
//                    ],
//                ],
//            ]
//        ];
        // 虽然match与fuzzy都能模糊查询，但二者能查询到的是不同的
        $query['sort']['age']['order'] = 'desc';
        $param['body'] = $query;
        dump($param);
        return $this->esObj->search($param);
    }

    protected function getIndex(){
        $this->indexDb = input('db_name', 'student');
    }

    /**
     * 插入，id必须入参，id重复会报错
     * @return Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
     * @throws Elasticsearch\Exception\ClientResponseException
     * @throws Elasticsearch\Exception\MissingParameterException
     * @throws Elasticsearch\Exception\ServerResponseException
     */
    public function save(){
        $res = $this->esObj->create(input());
        return $res;
    }

    /**
     * 根据有没有入参id来判断是插入还是更新
     * @return Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
     * @throws Elasticsearch\Exception\ClientResponseException
     * @throws Elasticsearch\Exception\MissingParameterException
     * @throws Elasticsearch\Exception\ServerResponseException
     */
    public function createOrEdit(){
        $res = $this->esObj->index(input());
        return $res;
    }

    /**
     * 更新api，id必传
     * @param int $id
     * @return Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
     * @throws Elasticsearch\Exception\ClientResponseException
     * @throws Elasticsearch\Exception\MissingParameterException
     * @throws Elasticsearch\Exception\ServerResponseException
     */
    public function update($id = 0){  // 在形参这可以拿到id，在input()也可以拿到id=>1
//        $res = $this->esObj->update(input());
        $param['body'] = [
            'user_name' =>  'cccc',
            'password' =>  'ddddd',
        ];
        $param['q']['user_name'] = '1111111';
        $param['index'] = $this->indexDb;
        dump($param);
        $res = $this->esObj->updateByQuery($param);
        return $res;
    }

    public function bulk(){
        $data = Users::limit(2)
//            ->order('id desc')
            ->select()->toArray();
//        $add = ['index'=>$this->indexDb];
        $arr = [];
        foreach ($data as $item){
            $arr[]['create']['_index'] = $this->indexDb;    // 1、纯新增
//            $arr[]['create'] = [      // 2、create这里入参了_id，不新增也不更新，但$res是成功的
//                '_index' => $this->indexDb,
//                '_id' => $item['id'],
//            ];
//            $arr[]['index'] =     // 3、index里入参了_id，如果id存在就更新，不存在就新增；看下面
//                ['_index' => $this->indexDb,
//                '_id' => $item['id'],
//            ];

            $arr[] = [
                'user_name' => 'aaaaaaa',
                'password' => 'bbbbbb',
            ];
        }
        $add['body'] = $arr;
        dump($add);
        $res = $this->esObj->bulk($add);
        halt($res);
    }


}