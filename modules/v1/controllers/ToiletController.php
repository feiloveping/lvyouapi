<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/22
 * Time: 10:27
 */

namespace app\modules\v1\controllers;


use app\modules\components\helpers\FeiArr;
use app\modules\components\helpers\FeiMaplocation;
use app\modules\components\helpers\GeoHash;
use app\modules\components\helpers\MyImg;
use app\modules\v1\models\Toilet;

use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

class ToiletController extends ActiveController
{
    public $modelClass = '' ;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    // 获取web端首页的厕所列表 - 按照距离排序
    public function actionToiletList2()
    {

        $request    =   \Yii::$app->request;
        $page = $request->get('page');

        $lng        =   (float) $request->get('lng',98.50);
        $lat        =   (float) $request->get('lat',25.03);


        $toilet = $this->runAction('toilet-all-list',['lat'=>$lat,'lng'=>$lng]);

        if(!$toilet) return ['code'=>404,'data'=>'','msg'=>'未找到数据'];

        $api_url = \Yii::$app->params['api_url'];
        $app_url = \Yii::$app->params['app_url'];

        // 对图片路径进行拼接, 并且处理距离用户多少米
        $map    =   new FeiMaplocation();
        $myImg = new MyImg();
        $toiletAll = [];
        foreach ($toilet as $v)
        {
            $litpic = $app_url . $v['litpic'];
            $filename = $myImg->getImg($litpic,'toilet','50');
            // 获得当前图片名
            if($filename)
                $litpics = $api_url . '/img/lvyou/toilet/' .$filename;
            else
                $litpics = $api_url . '/img/lvyou/toilet/' .pathinfo($v['litpic'])['basename'];

            $toiletAll[] = [
                'id'=>$v['id'],
                'title'=>$v['title'],
                'litpic'=>$litpics,
                'lng'=>$v['lng'],
                'lat'=>$v['lat'],
                'issmarty'=>$v['issmarty'],
                'threetype'=>$v['threetype'],
                'address'=>$v['address'],
                'opentime'=>$v['opentime'],
                'closetime'=>$v['closetime'],
                'mancount'=>$v['mancount'],
                'womancount'=>$v['womancount'],
                'metre'=>$map->getMetre($v['metre']),
                ];
        }
        // 对其进行分页
        $pageObj = new FeiArr();
        $result = $pageObj->pageArr($toiletAll,$page);
        if(empty($result))
            return ['code'=>404,'data'=>'','msg'=>'未找到数据'];
        else
            return ['code'=>200,'data'=>['toilet'=>$result['data'],'pagecount'=>$result['pagecount']],'msg'=>'ok'];
    }

    // 对所有数据进行排序
    public function actionToiletAllList($lat,$lng)
    {
        //获取所有数据 并缓存
        $cache           =      \Yii::$app->cache;
        $key             =      'alltoilet';
        if(!$cache->exists($key))
        {
            $allToilet       =      Toilet::getAll();
            $cache->add($key,$allToilet,600);
        }
        $allToilet      =       $cache->get('alltoilet');
        // 计算出距离
        $geoHash    =   new GeoHash();
        foreach($allToilet as $key=>$val)
        {
            $distance = $geoHash->getDistance($lat,$lng,$val['lat'],$val['lng']);
            $allToilet[$key]['metre'] = $distance;
        }
        $re = FeiArr::my_sort($allToilet,'metre');
        return $re;
    }

    // 首页厕所详情
    public function actionToiletDetail()
    {
        $id =   \Yii::$app->request->get('id');

        $cache      =       \Yii::$app->cache;
        $key        =       'toilet:id:'.$id;


        if(!$cache->get($key))
        {
            $toilet =  Toilet::toiletDetail($id);
            $cache->set($key,$toilet,60);
        }else {
            $toilet = $cache->get($key);
        }

        // 处理是否为第三卫生间
        $toilet['threetype'] = explode(',',$toilet['threetype']);

        $app_url = \Yii::$app->params['app_url'];
        if(empty($toilet)) return ['code'=>404,'data'=>'','msg'=>'未找到数据'];

        $api_url = \Yii::$app->params['api_url'];
        // 处理图片
        $myImg = new MyImg();
        $litpic = $app_url . $toilet['litpic'];
        $filename = $myImg->getImg($litpic,'toilet','50');
        // 获得当前图片名
        if($filename)
            $toilet['litpic'] = $api_url . '/img/lvyou/toilet/' .$filename;
        else
            $toilet['litpic'] = $api_url . '/img/lvyou/toilet/' .pathinfo($toilet['litpic'])['basename'];

        return ['code'=>200,'data'=>$toilet,'msg'=>''];

    }

    // 或的离用户最近的距离的10个坐标 (geohash 并优化排序)
    public function actionMemberNearToilet()
    {
        $request= \Yii::$app->request;
        $lng        =   (float) $request->get('lng',98.486097);
        $lat        =   (float) $request->get('lat',25.041022);

        // 计算出相对比较近的几个位置
        $geoHash    =   new GeoHash();
        $geohashStr = substr($geoHash->encode($lat,$lng),0,5);

        $toilet = Toilet::getNearToilet($geohashStr);
        if(empty($toilet))
            return ['code'=>404,'data'=>'','msg'=>'未找到数据'];
        // 计算出距离
        foreach($toilet as $key=>$val)
        {
            $distance = $geoHash->getDistance($lat,$lng,$val['lat'],$val['lng']);
            $toilet[$key]['metre'] = $distance;
        }
        ArrayHelper::multisort($toilet,'metre',SORT_ASC);
        // 获取前十个
        $toilet =  array_slice($toilet,0,10);
        return ['code'=>200,'data'=>$toilet,'msg'=>''];

    }

    // 获得所有的厕所经纬度,然后更新其geohash
    public function actionUpdateToilet()
    {
        $toilet = Toilet::getAll();
        $geohash = new Geohash();
        $toiletModel = \Yii::$app->db->createCommand();
        foreach ($toilet as $key=>$item) {
            // 计算geohash
            $geohashStr = $geohash->encode($item['lat'],$item['lng']);
            $toiletModel->update(Toilet::tableName(), ['geohash' => $geohashStr], 'id = '.$item['id'])->execute();
        }

        echo 'ok';

    }

    // 公厕数据上报
    public function actionPostData1()
    {

        $url            =   "https://apitest.bdc.ybsjyyn.com";
        $path           =   '/toilet/status/report';
        $id             =   1231504;
        $devid          =   'sdhihfdjewdfdfs498toilet';
        $device_secret  =   "301647cd271b5843aac00fa92f28dcd6";
        $nonce = time() . rand(1000,9999);
        $time = time() ;

        $query = array(
            'mode'      =>  1,
            'id'        =>  $id,
            'devid'     =>  $devid,
            'nonce'     =>  $nonce,
            'timestamp' =>  $time,
        );
        $data           =   [
            'time'  => $time,
            'male'  =>  [
                'in'=>1,
                'out'=>2,
                'stay'=>66
            ],
            'female'  =>  [
                'in'=>1,
                'out'=>2,
                'stay'=>3
            ],
            'stay'  =>  [
                'in'=>1,
                'out'=>2,
                'stay'=>66
            ],
        ];
        $post_data           =   json_encode($data);
        ksort($query, SORT_STRING);
        $query_string = '';
        foreach($query as $key => $value) {
            $query_string .= rawurlencode($key).'='.rawurlencode($value).'&';
        }
        $query_string   = rtrim($query_string, '&');
        $param   = $path.'?'.$query_string . $post_data;
        $sign  = base64_encode(hash_hmac('sha1', $param  , $device_secret, true));
        $url            = $url . $path.'?'.$query_string  . '&sign=' .rawurlencode($sign);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            echo '错误:'.curl_error($ch);
        }
        curl_close($ch);
        echo "{\"code\":0,\"msg\":\"\",\"data\":{\"total\":1,\"num\":1}}";
        //print_r($output);
    }

    // 公厕数据接收存储

    /**
     * @param
     * 接收数据,并存储,再交给腾讯处理
     */
    public function actionGetData()
    {
        // 拿数据,存储
        $request = \Yii::$app->request;
        $redis = \Yii::$app->redis;
        $timestamp = $request->get('timestamp');
        $data       =   json_decode($request->get('data'),true);
        $key_auto = 'auto_init_toilet';
        $id = $redis->get($key_auto);
        $key = 'toilet:up:get:'.$id;
        $redis->set($key,json_encode($data));
        $redis->set($key_auto,$id + 1);
        return ['timestamp'=>$timestamp,'data'=>$data];
    }

    public function actionPostData()
    {
        $request = \Yii::$app->request;
        $redis = \Yii::$app->redis;
        $timestamp = $request->post('timestamp');
        $data      = $request->post('data');

        $key_auto = 'auto_init_toilet';
        $id = $redis->get($key_auto);
        $key = 'toilet:up:get:'.$id;
        $redis->set($key,json_encode($data));
        $redis->incr($key_auto);
        return ['timestamp'=>$timestamp,'data'=>$data];
    }













}