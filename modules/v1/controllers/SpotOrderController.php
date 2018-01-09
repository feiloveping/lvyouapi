<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/6
 * Time: 14:40
 */

namespace app\modules\v1\controllers;


use app\modules\components\helpers\MyDateFormat;
use app\modules\v1\models\MemberLinkman;
use app\modules\v1\models\MemberOrderTourer;
use app\modules\v1\models\Spot;
use app\modules\v1\models\SpotTicket;
use app\modules\v1\models\SpotTicketPrice;
use yii\web\Response;

class SpotOrderController extends DefaultController
{
    public $modelClass = '' ;
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;

    }

    // 根据景区id获取景区的门票和简单时间表
    public function actionGetSpotOrderMessage()
    {
        $spotid = \Yii::$app->request->get('id');
        $ticketmessage = SpotTicket::getTicketsBySpotId($spotid);
        if(empty($ticketmessage))
            return ['code'=>404,'data'=>'','msg'=>'景点门票信息没找到'];

        foreach ($ticketmessage as $k=>$v)
        {
           $time = SpotTicketPrice::getTicketTimeByTid($v['id']);
            $times = [];
            $nowtime = strtotime(date('Y-m-d',time()));
            // 对过期门票进行过滤 - 选择前两天的数据(若想要全部数据则不进行处理)
            foreach ($time as $k2=>$v2)
            {
                if($v2['day'] >= $nowtime) {
                    $times[] = $v2;
                    if(count($times) > 1 ) break ;
                }
            }
            $ticketmessage[$k]['timelist'] = $times ;
        }
        return ['code'=>200,'data'=>$ticketmessage,'msg'=>'ok'];
    }

    // 根据景点门票的获取当前门票的所有的时间数据
    public function actionGetSpotTicketTime($id)
    {
        $spotticketTime = SpotTicketPrice::getTicketTimeByTid($id);

        if ($spotticketTime){
            $mydateObj = new MyDateFormat();
            $spotticketTime =  $mydateObj->initDate($spotticketTime,'day');
            return ['code'=>200,'data'=>$spotticketTime,'msg'=>'ok'];
        }
        else
            return ['code'=>404,'data'=>'','msg'=>'景点门票信息没找到'];
    }


    /*
     * @params
     * @ticketid        门票id
     * @usedate         使用时间-时间戳
     * @linkman         联系人
     * @linktel         联系电话
     * @dingnum         预定数量
     * @remark          备注(可以为空)
     * */
    // 提交订单
    public function actionAddOrder()
    {
        // 对是否登录进行判断
        if(!$this->logsign) return ['code'=>401,'msg'=>'请登录','data'=>''];
        $result = \Yii::$app->request->post();
        $typeid =   \Yii::$app->params['typeid']['spot'];
        $data = [
            'typeid'        =>$typeid,                                  // 类型id
            'memberid'      =>$this->mid,                               // 用户id
            'suitid'        =>$result['ticketid'],                      // 门票的id
            'usedate'       =>date('Y-m-d',$result['usedate']), // 使用时间
            'linkman'       =>$result['linkman'],                       // 联系人
            'linktel'       =>$result['linktel'],                       // 联系电话
            'addtime'       =>time(),                                   // 订单创建时间
            'dingnum'       =>$result['dingnum'],                       // 预定数量
            'remark'        =>$result['remark'],                        // 备注
        ];

        // 对必须数据进行验证
        if(  empty($data['suitid']) || empty($data['usedate']) || empty($data['linkman']) ||
            empty($data['linktel']) || empty($data['dingnum']))
            return ['code'=>400,'data'=>'','msg'=>'参数不能为空'];

        // 根据门票id查询景点id
        $spotid                 =   SpotTicket::getDesByTicketId($data['suitid'])['spotid'];
        if(!$spotid) return ['code'=>4001,'data'=>null,'msg'=>'景点未找到'];

        // 根据景点id获得相关信息 , title,litpic,id,aid
        $spotEasyMessage                =   Spot::getEasySpotByid($spotid);
        $data['productaid']     =   $spotEasyMessage['aid'];
        $data['productautoid']  =   $spotid;
        $data['supplierlist']   =   $spotEasyMessage['supplierlist'];
        $data['litpic']         =   $spotEasyMessage['litpic'];
        $data['productname']    =   $spotEasyMessage['title'];

        // 对景点门票信息进行处理(价格)
        $spotmessage = SpotTicket::getTicketByTid($data['suitid']);
        // 对门票的最小和最大预定量进行判断
        if(!empty($spotmessage['buylimitnummin']) && $data['dingnum'] < $spotmessage['buylimitnummin'])
            return ['code'=>4001,'data'=>'','msg'=>'预定量超过最小限制'];
        elseif(!empty($spotmessage['buylimitnummax']) && $data['dingnum'] > $spotmessage['buylimitnummax'])
            return ['code'=>4001,'data'=>'','msg'=>'预定量超过最大限制'];
        // 选择当前日期的门票价格
        $ticket = SpotTicketPrice::getTicketDetailByTimeId($data['suitid'],$result['usedate']);
        if( $ticket['number'] != -1 && $ticket['number'] <1 )
            return ['code'=>4001,'data'=>'','msg'=>'库存不足,请选择其它商品'];
        $data['price']          = $ticket['adultprice'];
        $data['marketprice']    = $ticket['adultmarketprice'];

        // 对联系人进行初步处理(联系人以json形式传递id)
        $tourer = json_decode($result['tourer']);
        if(count($tourer) != $data['dingnum'])  return ['code'=>4001,'data'=>'','msg'=>'请选择游客信息'];

        // 根据条件查到所有的游客信息
        $linkmans = MemberLinkman::getLinkmanByIds($tourer);
        if(empty($linkmans)) return ['code'=>4002,'data'=>'','msg'=>'游客信息错误'];

        // 生成订单操作  - 订单状态直接为等待确认状态
        $data['status'] = 1;
        // 后期增加积分策略
        $data['needjifen'] = 0;
        // 生成订单操作
        $orderid = \Yii::$app->runAction('v1/member-order/add-order',['data'=>$data]);
        // 生成订单成功后减少库存 - 事务 (是付款减库存还是下单减库存,待定)
        // 对游客信息进行数据保存
        foreach ($linkmans as $k=>$v)
        {
            $linkmans[$k] = [
                'orderid'=>$orderid,
                'tourername'=>$v['linkman'],
                'sex'=>$v['sex'],
                'cardtype'=>$v['cardtype'],
                'cardnumber'=>$v['idcard'],
                'mobile'=>$v['mobile']
            ] ;
        }
        // 增加游客地址记录
        $re = \Yii::$app->db->createCommand()
            ->batchInsert(MemberOrderTourer::tableName(),['orderid','tourername','sex','cardtype','cardnumber','mobile'] , $linkmans)
            ->execute();

        if($re)
            return ['code'=>200,'data'=>'','msg'=>'订单创建成功'];
        else
            return ['code'=>200,'data'=>'','msg'=>'订单创建成功,游客添加失败'];
    }
}