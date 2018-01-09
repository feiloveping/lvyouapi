<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/15
 * Time: 11:10
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class HotelRoomPrice extends ActiveRecord
{

    // 根据房型id获得对应的时间关系
    public function getRoomPriceTimeByTid($id)
    {
        return HotelRoomPrice::find()
            ->select(['day','price','number',"FROM_UNIXTIME(day,'%Y-%m-%d') as mydays"])
            ->where('suitid='.$id)
            ->orderBy('day')
            ->asArray()->all();
    }

    //根据房型id和时间获得当前库存
    public function getNumberBySuitidDay($suitid,$day)
    {
        return HotelRoomPrice::find()
            ->select('number,price')
            ->where(['suitid'=>$suitid,'day'=>$day])
            ->asArray()

            ->one();
    }

}