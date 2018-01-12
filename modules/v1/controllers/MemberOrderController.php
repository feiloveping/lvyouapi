<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/8
 * Time: 9:29
 */

namespace app\modules\v1\controllers;


use app\modules\components\helpers\TengchongOrders;
use app\modules\v1\models\MemberOrder;
use app\modules\v1\models\MemberOrderTourer;
use app\modules\v1\models\TongyongOrderStatus;

class MemberOrderController extends DefaultController
{
    public $modelClass = '';

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if (!$this->logsign) {
            echo json_encode(['code' => 401, 'msg' => '请登录', 'data' => '']);
            exit();
        }
    }

    public function actionAddOrder(array $data)
    {
        // 根据不同途径拿到的数据进行订单生成
        $type = $data['typeid'];
        $data['ordersn'] = TengchongOrders::get_ordersn($type);
        $data['webid'] = 0;
        $data['ispay'] = 0;
        $data['status'] = 1;

        // 进行订单新增数据
        $orderobj = \Yii::$app->db;
        $orderobj->createCommand()->insert('sline_member_order', $data)->execute();
        $id = $orderobj->getLastInsertID();
        return $id;
    }

    // 获得所有的订单状态
    public function actionOrderStatus()
    {
        $key = 'orderstatus';
        $cache = \Yii::$app->cache;
        if (!$orderStatus = $cache->get($key)) {
            $orderStatus = TongyongOrderStatus::getOrderStatus();
            $cache->set($key, $orderStatus, 86400);
        }
        return ['code' => 200, 'msg' => 'ok', 'data' => $orderStatus];
    }

    // 获得订单中的模型id
    public function actionOrdersType()
    {
        $type = [
            ['typeid'=>0,'typename'=>'全部'],
            ['typeid'=>1,'typename'=>'线路'],
            ['typeid'=>2,'typename'=>'酒店'],
            ['typeid'=>5,'typename'=>'景点'],
            ['typeid'=>13,'typename'=>'团购'],
        ];
        return ['code'=>200,'data'=>$type,'msg'=>'ok'];
    }

    // 订单列表
    public function actionLister()
    {
        $request = \Yii::$app->request;
        $memberOrder = new MemberOrder();
        $mid = $this->mid;
        $typeid = $request->get('typeid', 0);
        $page = $request->get('page', 1);
        $lister = $memberOrder->lister($typeid, $mid, $page);

        // 判断列表是否为空
        if (empty($lister))
            return ['code' => 200, 'data' => ['order' => [], 'pagecount' => ''], 'msg' => '数据为空'];

        // 计算出每个订单的总价 - 整理自己需要的数据
        $order = [];
        foreach ($lister['order'] as $k => $v) {
            $order[] = [
                'id' => $v['id'],
                'typeid' => $v['typeid'],
                'status' => $v['status'],
                'ispinglun'=>$v['ispinglun'],
                'productname' => $v['productname'],
                'usedate' => $v['usedate'],
                'departdate' => $v['departdate'],
                'statusname' => $v['status_name'],
                'totalcount' => $memberOrder->totalCount($v['id']),
            ];
        }
        $data = ['order' => $order, 'pagecount' => $lister['pagecount']];
        return ['code' => 200, 'data' => $data, 'msg' => 'ok'];
    }

    // 订单编辑 get 获取信息
    public function actionDetail()
    {
        $request = \Yii::$app->request;
        $orderId = $request->get('id', null);
        $memberObi = new MemberOrder();
        $order = $memberObi->getDetail($orderId);

        if(empty($order)) return ['code'=>404,'msg'=>'信息未找到','data'=>[]];

        $order['totalcount'] = $memberObi->totalCount($orderId);

        // 根据订单id获得出行人信息
        $tourObi = new MemberOrderTourer();
        $tour = $tourObi->getTourByOrderId($orderId);
        $order['tour'] = $tour;
        return ['code'=>200,'msg'=>'ok','data'=>$order];
    }

    // 订单取消  只有等待付款的时候才能取消订单
    public function actionCancel()
    {
        $request = \Yii::$app->request;
        $orderId = $request->get('id', null);
        $orderObi = new MemberOrder();
        $orderDetail = $orderObi->getDetail($orderId);
        // 验证订单的状态和用户

        if ($orderDetail['status'] != 1 || $orderDetail['memberid'] != $this->mid)
            return ['code' => 403, 'msg' => '参数错误', 'data' =>[]];

        // 更新数据
        $order = MemberOrder::findOne($orderId);
        $order->status = 3;
        $re = $order->save();
        if($re)
            return ['code' => 200, 'msg' => 'ok', 'data' =>[]];
        else
            return ['code'=>404 , 'msg'=>'取消失败','data'=>[]];

    }

    // 待付款1 ,待消费2,待点评 5 , ispinlun 0
    public function actionOrderCondition()
     {
         $request = \Yii::$app->request;
         $memberOrder = new MemberOrder();
         $mid = $this->mid;
         $status = $request->get('status', 0);
         $page = $request->get('page', 1);

         // 判断参数的合法性
         if(!in_array($status,[1,2,5])) return ['code'=>403,'msg'=>'非法数据','data'=>[]];

         $lister = $memberOrder->listerCondition($status,  $page ,$mid);

         // 判断列表是否为空
         if (empty($lister))
             return ['code' => 200, 'data' => ['order' => [], 'pagecount' => ''], 'msg' => '数据为空'];

         // 计算出每个订单的总价 - 整理自己需要的数据
         $order = [];
         foreach ($lister['order'] as $k => $v) {
             $order[] = [
                 'id' => $v['id'],
                 'typeid' => $v['typeid'],
                 'productname' => $v['productname'],
                 'usedate' => $v['usedate'],
                 'departdate' => $v['departdate'],
                 'totalcount' => $memberOrder->totalCount($v['id']),
             ];
         }
         $data = ['order' => $order, 'pagecount' => $lister['pagecount']];
         return ['code' => 200, 'data' => $data, 'msg' => 'ok'];
     }



}

