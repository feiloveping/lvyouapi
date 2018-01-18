<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 18:05
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\Collection;
use yii\web\Response;


class CollectionController extends DefaultController
{

    public $modelClass = '' ;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;

    }

    // 添加到收藏 - 并增加到redis
    public function actionAddCollection($typeid,$indexid,$mid)
    {
        $collection = new Collection();
        $collection->typeid = $typeid;
        $collection->indexid = $indexid;
        $collection->memberid = $mid;
        $collection->cotime = date('Y-m-d H:i:s',time()) ;
        //$key = 'collect:type:'.$typeid. ':indexid:'.$indexid.':member:'.$memberid ;
        $key = 'collect:'.$typeid. ':'.$indexid.':'.$mid ;
        // 判断是否已经收藏
        $redis = \Yii::$app->redis;
        if($redis->get($key)){
            return false;
        }elseif (Collection::isCollection($typeid,$indexid,$mid)){
            $redis->set($key ,'1');         // 更新redis 数据
            return false ;
        }
        // 若未收藏,则进行收藏
        $redis->set($key ,'1');
        return $collection->save();

    }

    // 取消收藏 - 数据库和redis 缓存清除
    public function actionDelCollection($typeid,$indexid,$mid)
    {
        $redis = \Yii::$app->redis;
        $key = 'collect:'.$typeid. ':'.$indexid.':'.$mid ;
        if(! $redis->get($key) || !Collection::isCollection($typeid,$indexid,$mid) ){
            return false ;  // 用户未收藏该商品
        }
        $redis->del($key);
        return Collection::delCollection($typeid,$indexid,$mid) ;
    }
    // 批量删除收藏 - 根据收藏id删除
    public function actionDelCollectionByids(array $ids)
    {
        // 删除缓存数据
        // 先获取基本信息 , 便于清除缓存
        $collection =   Collection::getCollectionByids($ids);
        foreach ($collection as $k=>$v)
        {
            $redis = \Yii::$app->redis;
            $key = 'collect:'.$v['typeid']. ':'.$v['indexid'].':'.$v['mid'] ;
            $redis->del($key);
        }
        // 删除数据表中记录
       return Collection::deleteAll(['id'=>$ids]);
    }

}