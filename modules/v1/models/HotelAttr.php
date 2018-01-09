<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/2
 * Time: 15:27
 */

namespace app\modules\v1\models;


use app\modules\components\helpers\CateTree;
use yii\db\ActiveRecord;

class HotelAttr extends ActiveRecord
{

    // 獲得首頁的6個酒店屬性
    public function getHotelIndex()
    {
        $hotel =  HotelAttr::find()
            ->select('id,attrname')
            ->orderBy('id')
            ->where('pid>0 and isopen=1')
            ->limit(6)
            ->asArray()
            ->all();
        foreach ($hotel as $k=>$v)
        {
            $hotel[$k]['bookcount'] = Hotel::getHotelBookCountByAttrid($v['id'])['bookcount'];
        }
        return $hotel;
    }

    // 獲得所有的屬性
    public function getHotelAttr()
    {
        $hotel = HotelAttr::find()
            ->select('id,pid,attrname')
            ->where('isopen=1')
            ->asArray()
            ->all();
        $cate = new CateTree();
        return $cate->make_tree1($hotel);
    }

}