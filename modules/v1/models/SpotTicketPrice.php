<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/6
 * Time: 15:33
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class SpotTicketPrice extends ActiveRecord
{
    // 根据景点票id获得对应的时间关系
    public function getTicketTimeByTid($id)
    {
        return SpotTicketPrice::find()
            ->where(['ticketid'=>$id])
            ->orderBy('day')
            ->select(['day','adultprice','number',"FROM_UNIXTIME(day,'%Y-%m-%d') as mydays"])
            ->orderBy('day')
            ->asArray()->all();
    }

    // 根据景点id和门票id,时间获得当前门票的信息
    public function getTicketDetailByTimeId($ticketid,$usedate)
    {
        return SpotTicketPrice::find()
            ->select('adultprice,adultmarketprice,number')
            ->where(['ticketid'=>$ticketid,'day'=>$usedate])
            ->asArray()
            ->one();
    }



}
