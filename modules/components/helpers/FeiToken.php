<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/7
 * Time: 9:46
 */

namespace app\modules\components\helpers;




class FeiToken
{
    // 用户生成token,直接存放在redis中,对用关系为 用户名:token 过期时间为一个周
    public static function createToken($memberid,$key)
    {
        $token_before = 'memberid:'. $memberid . ':time:' . time() ;
        $token = MyEncrypt::passport_encrypt($token_before , $key ) ;
        $redis = \Yii::$app->redis;
        $redis->setex('member:token:'.$token , 2592000 , $memberid );

        return $token ;
    }

    public static function delToken($token)
    {
        $redis = \Yii::$app->redis;
        $redis->setex('member:token:'.$token , 1 , 1 );
    }
    public static function checkToken($token)
    {
        $redis = \Yii::$app->redis;
        $re = $redis->get('member:token:'.$token) ;
        return $re ;
    }
    public static function saveToken()
    {

    }


}