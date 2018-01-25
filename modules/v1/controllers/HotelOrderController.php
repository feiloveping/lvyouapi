<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/16
 * Time: 15:11
 */

namespace app\modules\v1\controllers;

use app\modules\components\helpers\MyDateFormat;
use app\modules\v1\models\Hotel;
use app\modules\v1\models\HotelRoom;
use app\modules\v1\models\HotelRoomPrice;
use app\modules\v1\models\MemberLinkman;
use app\modules\v1\models\MemberOrderTourer;

class HotelOrderController extends DefaultController
{
    public $modelClass='';


    // 选择所有的产品套餐    -   若有默认则选择默认
    public function actionGetHotelOrderMessage()
    {
        $request            =       \Yii::$app->request;
        $hotelid            =       $request->get('hotelid');
        $roomMessage        =       HotelRoom::getRoomBriefPriceById($hotelid);
        if(empty($roomMessage))
            return ['code'=>200,'msg'=>'酒店套餐信息未找到','data'=>''];
        // 根据酒店的id获得相关套餐的时间和库存
        foreach ($roomMessage as $k=>$v)
        {
            $time       =  HotelRoomPrice::getRoomPriceTimeByTid($v['id']);
            // 对过期套餐进行过滤
            $myDate = new MyDateFormat();
            $roomMessage[$k]['timelist'] = $myDate->initDate($time,'day');
        }
        return ['code'=>200,'data'=>$roomMessage,'msg'=>'ok'];
    }

    // 根据当前房间id获取所有时间的房间
    public function actionGetHotelRoomTime()
    {
        $request        =       \Yii::$app->request;
        $id             =       $request->get('id');
        $roomPrice      =       HotelRoomPrice::getRoomPriceTimeByTid($id);
        $todaytime      =       strtotime(date('Y-m-d',time()));
        $room           =       [];
        // 对时间进行处理 - 排除过期时间
        foreach ($roomPrice as $k=>$v)
        {
            if($v['day'] >= $todaytime) $room[] =   $v;
        }
        return     $room;
    }

    /*
     * @params
     * @roomid          房间号id
     * @usedate         使用时间-时间戳
     * @leavetime       离店时间-时间戳
     * @linkman         联系人
     * @linktel         联系电话
     * @dingnum         预定数量
     * @remark          备注(可以为空)
     * */
    // 酒店的产品id为 酒店id,具体的房间类型id为suitid
    // 提交订单
    public function actionAddOrder()
    {
        // 对是否登录进行判断
        if(!$this->logsign) return ['code'=>401,'msg'=>'请登录','data'=>''];

        // 组织参数
        $result         =       \Yii::$app->request->post();
        $typeid         =       \Yii::$app->params['typeid']['hotel'];
        $data = [
            'typeid'        =>$typeid,                                  // 类型id
            'memberid'      =>$this->mid,                               // 用户id
            'suitid'        =>$result['roomid'],                        // 房间类型id
            'usedate'       =>date('Y-m-d',$result['usedate']), // 使用时间 - 即开始时间
            'departdate'    =>date('Y-m-d',$result['leavetime']),// 离店时间
            'linkman'       =>$result['linkman'],                       // 联系人
            'linktel'       =>$result['linktel'],                       // 联系电话
            'addtime'       =>time(),                                   // 订单创建时间
            'dingnum'       =>$result['dingnum'],                       // 预定数量
            'remark'        =>$result['remark'],                        // 备注
        ];

        // 对必须数据进行验证
        if(empty($data['linkman']) || empty($data['linktel']) || empty($data['dingnum']))
            return ['code'=>400,'data'=>'','msg'=>'参数不能为空'];
        // 对房间信息进行处理(价格,名称)
        $hotelRoommessage       = HotelRoom::getRoomByRid($data['suitid']);

        if(!empty($hotelRoommessage['piclist']))
        $data['litpic']         =   explode(',',$hotelRoommessage['piclist'])[0];
        $hotelid                =   $hotelRoommessage['hotelid'];
        // 根据hotelid查找供应商id和前端显示的aid
        $hotelMessage           =   Hotel::hotelEasyDetail($hotelid);
        $data['productaid']     =   $hotelMessage['aid'];
        $data['productname']    =   $hotelMessage['title'] ;
        $data['productautoid']  =   $hotelid;
        $data['supplierlist']   =   $hotelMessage['supplierlist'];
        // 处理选择日期的房间库存和价格
        // 验证每天的库存 suitid 和day , 计算出总的房间价格
        $roomPrice = 0;
        $roombalance     =   0;


        for ($i = $result['usedate'] ; $i <$result['leavetime'] ; $i = $i+86400)
        {
            $roomNumberPrice = HotelRoomPrice::getNumberBySuitidDay($data['suitid'],$i);
            $number          =  $roomNumberPrice['number'];
            if($number != -1 && $number<1) return ['code'=>4001,'data'=>['day'=>$i],'msg'=>'库存不足,请选择其它商品'];
            $roomPrice       +=  $roomNumberPrice['price'];
            $roombalance     =   $roomNumberPrice['roombalance'];
        }
        // 处理单房差数量 和价格
        $data['roombalancenum'] = $result['roombalancenum'];
        $data['roombalance']    =   $roombalance;
        // 只记录所有房间的单价       -      付款金额 = 数量 * 单价 不存数据库,支付时根据订单id进行计算
        $data['price'] = $roomPrice;
        // 对联系人进行初步处理(联系人以,拼接形式传递id)
        $tourer = explode(',',$result['tourer']);
        if(count($tourer) != $data['dingnum'])  return ['code'=>4001,'data'=>[],'msg'=>'请选择游客信息'];

        // 根据条件查到所有的游客信息
        $linkmans = MemberLinkman::getLinkmanByIds($tourer);
        if(empty($linkmans)) return ['code'=>4002,'data'=>[],'msg'=>'游客信息错误'];

        // 生成订单操作  - 订单状态直接为等待确认状态
        $data['status'] = 1;
        // 后期增加积分策略
        $data['needjifen'] = 0;
        $orderid = \Yii::$app->runAction('v1/member-order/add-order',['data'=>$data]);

        // 生成订单成功后减少库存 - 事务 (是付款减库存还是下单减库存,待定)
        // 对游客信息进行数据保存
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
            return ['code'=>200,'data'=>[],'msg'=>'订单创建成功'];
        else
            return ['code'=>200,'data'=>[],'msg'=>'订单创建成功,游客添加失败'];
    }



}