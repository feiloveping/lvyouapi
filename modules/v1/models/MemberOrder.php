<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/10
 * Time: 11:07
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class MemberOrder extends ActiveRecord
{
    // 根据mid,typeid获得相应的订单
    public function lister($typeid,$mid,$page)
    {
        $query = MemberOrder::find()
            ->alias('mo')
            ->select('mo.id,ordersn,typeid,supplierlist,productaid,productname,productautoid as productid,
                    ispinlun as ispinglun,litpic,price,dingnum,mo.status,usedate,departdate,os.status_name
            ')
            ->leftJoin(TongyongOrderStatus::tableName() . ' os' , 'os.status=mo.status')
            ->where(['memberid'=>$mid]);
        if($typeid)
        {
            $query->andWhere(['typeid'=>$typeid]);
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $pages->page = $page -1 ;
        $order['order'] = $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $order['pagecount'] = $pagecount;
        return $order;
    }

    // 根据mid,typeid获得相应的订单
    public function listerCondition($status,$page,$mid)
    {
        $query = MemberOrder::find()
            ->select('id,ordersn,typeid,supplierlist,productaid,productname,productautoid as productid,
                    litpic,price,dingnum,status,usedate,departdate,memberid
            ')
            ->where(['memberid'=>$mid]);
        switch ($status)
        {
            case 1:
                $query->andWhere(['status'=>$status]);
                break;
            case 2:
                $query->andWhere(['status'=>$status]);
                break;
            case 5:
                $query->andWhere(['status'=>$status,'ispinlun'=>0]);
                break;
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $pages->page = $page -1 ;
        $order['order'] = $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $order['pagecount'] = $pagecount;
        return $order;
    }



    // 根据订单id统一计算订单总金额
    public function totalCount($id)
    {
        $order = MemberOrder::find()
            ->select('id,typeid,price,dingnum,usedate,departdate')
            ->where(['id'=>$id])
            ->one();
        switch ($order['typeid'])
        {
            case 1: // 线路 - 暂定
                $total = $order['price'] * $order['dingnum'];;
                break;
            case 2: // 酒店
                $day    =   (strtotime($order['departdate']) -  strtotime($order['departdate'])) / 86400 ;
                $total = $order['price'] * $order['dingnum'] * $day;
            case 5: // 景点
                $total = $order['price'] * $order['dingnum'];
                break;
            case 13: //团购
                $total = $order['price'] * $order['dingnum'];;
                break;
        }

        return $total;

    }

    // 根据订单id获取订单详细信息
    public function getDetail($id)
    {
        $order = MemberOrder::find()
            ->select('id,ordersn,typeid,price,productautoid,productname,addtime,status,memberid,
            dingnum,usedate,departdate,linkman,linktel,remark')
            ->where(['id'=>$id])
            ->asArray()
            ->one();

        // 获取产品名称
//        switch ($order['typeid'])
//        {
//            case 1: // 线路 - 暂定
//                $title  =   Line::lineEasyDetail($order['productautoid'])['title'];
//                break;
//            case 2: // 酒店
//                $title  =   Hotel::hotelEasyDetail($order['productautoid'])['title'];
//            case 5: // 景点
//                $title  =   Spot::getEasySpotByid($order['productautoid'])['title'];
//                break;
//            case 13: //团购
//                $title  =   Tuan::tuanEasyDetail($order['productautoid'])['title'];
//                break;
//        }
//        $order['title'] = $title;

        return $order;



    }


}