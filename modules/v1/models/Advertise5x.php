<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 10:28
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Advertise5x extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%advertise_5x}}';
    }

    public function getIndexBanner()
    {
        return Advertise5x::find()
            ->select('id,adsrc,adname,adlink')
            ->where('id=1')
            ->asArray()->one();
    }

}