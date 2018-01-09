<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/12
 * Time: 14:30
 */

namespace app\modules\v1\controllers;

use app\modules\components\helpers\ChuanglanSms;
use app\modules\components\helpers\FeiValidate;
use app\modules\components\helpers\MyEncrypt;
use app\modules\v1\models\SmsMsg;
use app\modules\v1\models\SmsSendlog;


class MessageController extends DefaultController
{
    public $modelClass = '' ;

    public function actionSendVerifyMessage($mobile,$msgtype,$ip )
    {
        if(! FeiValidate::isMobile($mobile)) return ['code'=>400,'data'=>'','msg'=>'手机号格式不正确'] ;
        /*生成短信验证码*/
        $verify = MyEncrypt::createVerifyNum(6);
        /* 验证短信发送次数 */
        $msgcount = SmsSendlog::MesCountLimit(['mobile'=>$mobile,'ip'=>$ip]);
        if($msgcount > 10 )  return ['code'=>402,'data'=>'','msg'=>'改手机号或ip短信发送过多'] ;
        /* 发送短信验证码   */
        $strings = SmsMsg::getMsgByType($msgtype)['msg'] ;
        $strings = str_replace('{#WEBNAME#}','本站',$strings);
        $strings = str_replace('{#CODE#}',$verify,$strings);
        $msg = new ChuanglanSms();
        $re = $msg->sendSMS($mobile,$strings);
        if(! $re) return ['code'=>501,'data'=>'','msg'=>'短信发送失败'] ;
        /* 对短信,验证码进行存储 */
        $redis = \Yii::$app->redis;
        $redis->set('sms:send:' . $mobile ,$verify) ;
        $redis->expire('sms:send:' . $mobile,120);     // 过期时间60秒
        $logdata = [
            'ip'=>$ip,
            'mobile'=>$mobile,
            'verify'=>$verify,
            'time'=>time(),
        ] ;
        $re = \Yii::$app->db->createCommand()->insert(SmsSendlog::tableName(),$logdata)->execute();
        return ['code'=>200 , 'data'=>'' , 'msg'=>'ok'] ;
    }

    public function actionCheckVerifyMessage($mobile,$verify)
    {
        $redis = \Yii::$app->redis;
        $redis_verify = $redis->get('sms:send:' . $mobile ) ;
        if($verify != $redis_verify)
        {
            return ['code'=>400,'data'=>'','msg'=>'验证码错误'] ;
        }
        else{
            // 验证码验证成功后,增加缓存时间,提高下一步的操作时间
            $redis->expire('sms:send:' . $mobile,100);
            return ['code'=>200,'data'=>'','msg'=>'ok'];
        }
    }
}