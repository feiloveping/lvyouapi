<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/2
 * Time: 16:34
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class HotelRank extends ActiveRecord
{
    public function getRank()
    {
        return Hotelrank::find()
            ->select(['id','hotelrank'])
            ->orderBy('orderid')
            ->asArray()
            ->all();
    }

}