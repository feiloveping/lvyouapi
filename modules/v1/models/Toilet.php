<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/21
 * Time: 16:37
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Toilet extends ActiveRecord
{

    public function getToiletList($page)
    {
        $query  =   Toilet::find()->select('id,title,litpic,lng,lat,issmarty,threetype,address,
        opentime,closetime,mancount,womancount')->where('ishidden=0');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 10;
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $pages->page = $page -1 ;
        $toilet['pagecount']    =   $pagecount;
        if($page > $pagecount) return false;
        $toilet['toilet'] = $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        return $toilet;

    }

    public function toiletDetail($id)
    {
        return Toilet::find()->select('id,title,litpic,lng,lat,issmarty,threetype,address,
        opentime,closetime,mancount,womancount')
            ->where('id='.$id)->one();
    }


    // 获得所有的toilet - 比较用户距离
    public function getAll()
    {
        return Toilet::find()->select('id,title,litpic,lng,lat,issmarty,threetype,address,
        opentime,closetime,mancount,womancount')->where('ishidden=0')->asArray()->all();
    }

    // 根据geohash获得附近的地址
    public function getNearToilet($geohash)
    {
        return Toilet::find()
            ->select('id,title,lng,lat,address,geohash')
            ->where(['like','geohash',$geohash.'%',false])
            ->asArray()->all();
    }

}