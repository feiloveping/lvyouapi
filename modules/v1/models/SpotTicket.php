<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 15:55
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class SpotTicket extends ActiveRecord
{
    // 根据景点id获取相应信息
    public function getTicketBySpotId($spotid)
    {
        $time = strtotime(date('Y-m-d' , time()));
        $ticket = SpotTicket::find()
            ->alias('st')
            ->select('st.id,st.title,st.lastoffer,st.tickettypeid,
                st.day_before,st.hour_before,st.minute_before,stt.kindname,sp.number')
            ->where(['st.spotid'=>$spotid])
            ->orderBy('st.displayorder')
            ->leftJoin('sline_spot_ticket_type as stt','stt.id=st.tickettypeid')
            // 计算当天余票
            ->leftJoin('(select ticketid,number from sline_spot_ticket_price where spotid='
                .$spotid . ' and day=' . $time  . ') as sp' , 'sp.ticketid=st.id')
            ->asArray()->all();

        // 对门票类型进行整合
        $result = [];
        if(!empty($ticket))
        {
            foreach ($ticket as $key => $info) {
                $result[$info['tickettypeid']][] = $info;
            }
        }

        return $result ;
    }

    // 根据门票id获取门票说明
    public function getDesByTicketId($id)
    {
        return SpotTicket::find()
            ->select('spotid,description')
            ->where('id='.$id)
            ->asArray()->one();
    }

    // 根据门票id获得 景区门票的详细信息
    public function getTicketByTid($id)
    {
        return  SpotTicket::find()->where(['id'=>$id])
            ->select(['id','title','sellprice','ourprice','buylimitnummin','buylimitnummax',
                'day_before','hour_before','minute_before'])
            ->asArray()
            ->one();
    }

    // 根据景点id获得 套餐信息
    public function getTicketsBySpotId($id)
    {
        return  SpotTicket::find()
            ->alias('st')
            ->select(['st.id','st.title','st.sellprice','tt.kindname'])
            ->where(['st.spotid'=>$id])
            ->leftJoin(SpotTicketType::tableName() . ' tt' ,'st.tickettypeid=tt.id' )
            ->asArray()->all();
    }
}