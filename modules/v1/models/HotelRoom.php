<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/15
 * Time: 11:03
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class HotelRoom extends ActiveRecord
{
    // 根据酒店id获得所有的房间以及房间的详细说明,以及当天报价和库存
    public function getRoomById($id)
    {
        $today = strtotime(date('Y-m-d',time()));
        return HotelRoom::find()
            ->select('hr.id,hr.roomname,hrp.price,hr.sellprice as marketprice,hrp.number,hr.piclist,hr.roomstyle,
            hr.roomarea,hr.roomwindow,hr.roomfloor,hr.breakfirst,hr.number as roomnum,hr.computer,hr.roomstyle')
            ->alias('hr')
            ->where('hr.hotelid=' . $id)
            ->leftJoin(HotelRoomPrice::tableName() . ' as hrp','hrp.day=' . $today. ' and hrp.hotelid=' .$id .' and hrp.suitid=hr.id' )
            ->asArray()->all();
    }


    // 根据酒店id获得所有房间的简单说明和当天的报价与库存
    public function getRoomBriefPriceById($id)
    {
        $today = strtotime(date('Y-m-d',time()));
        return HotelRoom::find()
            ->select('hr.id,hr.roomname,hrp.price,hr.sellprice as marketprice,hrp.number')
            ->alias('hr')
            ->where('hr.hotelid=' . $id)
            ->leftJoin(HotelRoomPrice::tableName() . ' as hrp','hrp.day=' . $today. ' and hrp.hotelid=' .$id .' and hrp.suitid=hr.id' )
            ->asArray()->all();
    }

    // 根据房间id获得相应的信息
    public function getRoomByRid($id)
    {
        return HotelRoom::find()
            ->select('roomname,piclist,hotelid')
            ->where(['id'=>$id])
            ->asArray()
            ->one();
    }

    // 根据房间id获得详细信息
    public function getRoomDetailById($id)
    {
        $today = strtotime(date('Y-m-d',time()));
        return HotelRoom::find()
            ->select('hr.id,hr.roomname,hrp.price,hr.sellprice as marketprice,hrp.number')
            ->alias('hr')
            ->where('hr.id=' . $id)
            ->leftJoin(HotelRoomPrice::tableName() . ' as hrp','hrp.day=' . $today. ' and hrp.hotelid=' .$id .' and hrp.suitid=hr.id' )
            ->asArray()->all();
    }



}