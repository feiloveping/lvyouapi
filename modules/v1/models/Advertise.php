<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/1
 * Time: 16:34
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Advertise extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%advertise_5x}}";
    }

    public function getSpotBanner()
    {
        return Advertise::find()
            ->select('id,adsrc,adlink,adname')
            ->where("prefix='spot_index' and is_show='1' and is_pc='0'")
            ->orderBy('modtime desc')
            ->limit(1)
            ->asArray()->one();
    }


    public function getHotelBanner()
    {
        return Advertise::find()
            ->select('id,adsrc,adlink,adname')
            ->where("prefix='hotel_index' and is_show='1' and is_pc='0'")
            ->orderBy('modtime desc')
            ->limit(1)
            ->asArray()->one();
    }


    public function getIndexBanner()
    {
        return Advertise::find()
            ->select('id,adsrc,adname,adlink')
            ->where('id=1')
            ->asArray()->one();
    }

    // 获得攻略的bannner
    public function getArticleBanner()
    {
        return Advertise::find()
            ->select('id,adsrc,adlink,adname')
            ->where("prefix='article_index' and is_show='1' and is_pc='0'")
            ->orderBy('modtime desc')
            ->limit(1)
            ->asArray()->one();
    }

}