<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 16:32
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Tuan extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%tuan}}';
    }

    public function getIndexTuan()
    {
        $app_url = \Yii::$app->params['app_url'];
        $now = time() ;

        return Tuan::find()
            ->select('id,title,shortname,sellprice,price,concat(\'' .$app_url. '\',`litpic`) as litpic,bookcount,satisfyscore,endtime,')
            ->where('endtime>'. $now .' and ishidden=0 and litpic is not null')
            ->orderBy('satisfyscore desc')
            ->limit(6)
            ->asArray()->all();
    }


    // 根据id获取收藏所需要的信息
    public function collectionMessage($id)
    {
        return Spot::find()
            ->select('id as indexid,price,litpic,bookcount,title,iconlist,satisfyscore')
            ->where('id='.$id)
            ->asArray()->one();
    }
}