<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/2
 * Time: 10:10
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class SpotPricelist extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%spot_pricelist}}";
    }


    // 獲得所有的價格區間
    public function getPriceList()
    {
        $price = SpotPricelist::find()
            ->select('id,min,max')
            ->orderBy('id')
            ->asArray()
            ->all();
        // 添加最大記錄
        $priceMax = end($price) ;
        array_push($price , ['id'=>$priceMax['id'] + 1,'min'=>$priceMax['max'] + 1 , 'max'=>null]);
        // 增加 0 - 0
        array_unshift($price,['id'=>'0','min'=>'全部','max'=>'全部']);
        return $price;
    }

    // 根據價格區間id獲得單條數據
    public function getPriceById($id)
    {
        // 判斷id是否為最大值
        $maxPrice = SpotPricelist::getMaxId();
        if($id > $maxPrice['id'])
        {
            return ['id'=>$id,'min'=>$maxPrice['min'] + 1 ,'max'=> 'max' ];
        }

        return SpotPricelist::find()
            ->select('id,min,max')
            ->where('id='.$id)
            ->asArray()
            ->one();
    }

    // 得到最大id
    public function getMaxId()
    {
        return SpotPricelist::find()
            ->select('id,min,max')->limit(1)->orderBy('id desc')
            ->asArray()
            ->one();
    }
}