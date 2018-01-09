<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/2
 * Time: 16:13
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class HotelPricelist extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%hotel_pricelist}}';
    }


    public function getPriceList()
    {
        $price = HotelPricelist::find()->select('id,min,max')->orderBy('id')->asArray()->all();
        $maxPrice = end($price);
        array_push($price,['id'=>$maxPrice['id'] + 1 , 'min'=> $maxPrice['max'] + 1 ,'max'=>null]);
        return $price;
    }

    public function getPriceListById($id)
    {
        return HotelPricelist::find()
            ->select('id,min,max')
            ->where('id='.$id)
            ->asArray()
            ->one();
    }

    public function getPriceMaxId()
    {
        return HotelPricelist::find()
            ->select('id,max')
            ->orderBy('id desc')
            ->limit(1)
            ->asArray()
            ->one();
    }


}