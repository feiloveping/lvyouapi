<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/9
 * Time: 10:58
 */

namespace app\modules\v1\controllers;

use app\modules\components\helpers\FeiToken;
use app\modules\components\helpers\FeiValidate;
use app\modules\v1\models\Member;
use yii\web\Response;

class LoginController extends DefaultController
{
    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index'],$actions['delete'], $actions['create']);
        return $actions;
    }

    public function actionLogin()
    {
        $request = \Yii::$app->request;
        $mobile = $request->post('mobile');
        $password = $request->post('password');
        $data = [
            'mobile'    =>  $mobile,
            'pwd'  =>  $password,
        ] ;
        if(empty($data['mobile']) || empty($data['pwd'])) return ['code'=>401,'data'=>'','msg'=>'手机号或密码不能为空'];
        $data['pwd'] = md5($data['pwd']);

        $member = Member::getMember($data) ;

        if(! FeiValidate::isMobile($data['mobile']) || empty($member) ) return ['code'=>401,'data'=>'','msg'=>'手机号或密码错误'];
        // 生成token
        $token = FeiToken::createToken($member['mid'],\Yii::$app->params['myEncrypt_key']);
        // 更新登录时间
        $Member = Member::findOne($member['mid']);
        $Member->logintime = time();
        $Member->save();

        // 获得融云token
        $re = \Yii::$app->runAction('v1/rongyun/gettoken',['userid'=>$member['mid'],'name'=>$member['nickname']]);
        $ry_token = json_decode($re,true)['token'];
        return ['code'=>200,'data'=>['token'=>$token,'ry_token'=>$ry_token] , 'msg'=>'ok'] ;
    }

    public function actionLogout()
    {
        $token = \Yii::$app->request->headers['token'] ;
        FeiToken::delToken($token);
        return ['code'=>200,'data'=>null , 'msg'=>'退出成功'] ;
    }


    // 登录和忘记密码的短信验证码发送
    public function actionSendMessage()
    {
        $request = \Yii::$app->request;
        $type = $request->post('msgtype') ;
        $mobile = (int) $request->post('mobile');
        if(empty($type) || empty($mobile) ) return ['code'=>404,'data'=>'','msg'=>'手机号参数不能为空'];
        $ip = ip2long($request->getUserIP());

        $Member = Member::findOne(['mobile'=>$mobile]);

        switch ($type)
        {
            case 1 :
                $msgtype = 'reg_msgcode' ;
                if($Member) return ['code'=>4001,'data'=>'','msg'=>'用户已注册'];
                break;
            case 2:
                $msgtype = 'reg_findpwd' ;
                if(!$Member) return ['code'=>4002,'data'=>'','msg'=>'用户号未注册'];
                break;
        }
        return \Yii::$app->runAction('v1/message/send-verify-message',['mobile'=>$mobile,'msgtype'=>$msgtype,'ip'=>$ip]);
    }

    public function actionCheckMessage()
    {
        $request = \Yii::$app->request;
        $mobile = $request->post('mobile');
        $verify = $request->post('verify',1);
        return \Yii::$app->runAction('v1/message/check-verify-message',['mobile'=>$mobile,'verify'=>$verify]);
    }

    public function actionRegister()
    {
        $request = \Yii::$app->request;
        $redis = \Yii::$app->redis;
        $mobile =  $request->post('mobile');
        $password = $request->post('password');
        $verify = $request->post('verify' , 1);
        // 对验证码再次验证 - 隐含
        $redis_verify = $redis->get('sms:send:' . $mobile ) ;
        if($verify != $redis_verify)   return ['code'=>400,'data'=>'','msg'=>'注册失败,请重新发送短信验证'] ;

        $ip = $request->getUserIP();
        if(empty($mobile) || empty($password) || ! FeiValidate::isMobile($mobile)) return ['code'=>400,'data'=>'','msg'=>'手机号或密码不能为空'] ;
        if(Member::hasMember(['mobile'=>$mobile]) > 0) return ['code'=>4001,'data'=>'','msg'=>'该手机号已经注册'] ;

        $data = [
            'mobile'=>$mobile,
            'pwd'=>md5($password),
            'nickname'=> substr($mobile,0,5) . '***' ,
            'jointime'=>time(),
            'joinip'=>$ip,
            'regtype'=>0
        ];
        $re = \Yii::$app->db->createCommand()->insert(Member::tableName(),$data)->execute();

        if(! $re)
            return ['code'=>403,'data'=>'','msg'=>'注册失败'] ;
        else{
            // 生成token并返回
            $where  =   ['mobile'=>$mobile];
            $member    =   Member::getMember($where);
            $token = FeiToken::createToken($member['mid'],\Yii::$app->params['myEncrypt_key']);

            // 获得融云token
            $re = \Yii::$app->runAction('v1/rongyun/gettoken',['userid'=>$member['mid'],'name'=>$member['nickname']]);
            $ry_token = json_decode($re,true)['token'];
            return ['code'=>200,'data'=>['token'=>$token,'ry_token'=>$ry_token] , 'msg'=>'ok'] ;
        }
    }

    // 提交修改的密码
    public function actionModifyPass()
    {
        // 直接对手机号的密码进行修改
        $request = \Yii::$app->request;
        $mobile = $request->post('mobile',1);
        $pwd = $request->post('pwd',1);
        $newpwd = $request->post('newpwd',2);
        if($pwd !== $newpwd) return ['code'=>400,'data'=>'','msg'=>'请保持两次密码输入一致'];
        // 对密码复杂度进行判断
        if (strlen($newpwd) < 6) return ['code'=>4001,'data'=>'','msg'=>'长度不能小于6位'];
        // 更新密码
        $Member = Member::findOne(['mobile'=>$mobile]);
        if(!$Member) return ['code'=>4002,'data'=>'','msg'=>'用户不存在'];
        $Member->pwd = md5($newpwd);
        if($Member->save() != false)
            return ['code'=>200,'data'=>'','msg'=>'密码修改成功.请重新登录'];
        else
            return ['code'=>4002,'data'=>'','msg'=>'密码修改失败.请重新修改'];

    }







}