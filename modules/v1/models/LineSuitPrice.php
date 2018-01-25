<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/19
 * Time: 11:07
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class LineSuitPrice extends ActiveRecord
{
    public function getSuitDetailList($suitid , $limit='')
    {
        $nowday = strtotime(date('Y-m-d',time()));

        $query =  LineSuitPrice::find()
            ->select('lineid,suitid,day,childprice,oldprice,adultprice,description,number,roombalance')
            ->where(['suitid'=>$suitid])
            ->andWhere(['>=','day',$nowday])
            ->orderBy('day');

        if($limit)
            $query->limit($limit);

        $lineSuit = $query
            ->asArray()
            ->all();
        return $lineSuit;
    }


    // 根据suitid,和day 或的相应的信息
    public function getSuitPriceByUseDay($suitid,$useday)
    {
        return LineSuitPrice::find()
            ->select('lineid,suitid,day,childprice,oldprice,adultprice,number,roombalance')
            ->where(['suitid'=>$suitid,'day'=>$useday])
            ->asArray()
            ->one();
    }
}