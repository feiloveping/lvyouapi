<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/8
 * Time: 9:29
 */

namespace app\modules\v1\controllers;


use app\modules\components\helpers\TengchongOrders;

class MemberOrderController extends DefaultController{
    public $modelClass = '' ;

    public function actionAddOrder(array $data)
    {
        // 根据不同途径拿到的数据进行订单生成
        $type = $data['typeid'];
        $data['ordersn'] = TengchongOrders::get_ordersn($type);
        $data['webid'] = 0 ;
        // 进行订单新增数据
         $orderobj = \Yii::$app->db;
         $orderobj->createCommand()->insert('sline_member_order',$data)->execute();
         $id = $orderobj->getLastInsertID();
         return $id;
    }


}