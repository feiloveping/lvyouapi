<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/7
 * Time: 11:45
 */

namespace app\modules\components\helpers;


class MyEncrypt
{
    /*
    *功能：对字符串进行加密处理
    *参数一：需要加密的内容
    *参数二：密钥
    */
    public static  function passport_encrypt($str,$key){ //加密函数
        srand((double)microtime() * 1000000);
        $encrypt_key=md5(rand(0, 32000));
        $ctr=0;
        $tmp='';
        for($i=0;$i<strlen($str);$i++){
            $ctr=$ctr==strlen($encrypt_key)?0:$ctr;
            $tmp.=$encrypt_key[$ctr].($str[$i] ^ $encrypt_key[$ctr++]);
        }
        return base64_encode(self::passport_key($tmp,$key));
    }
    /*
    *功能：对字符串进行解密处理
    *参数一：需要解密的密文
    *参数二：密钥
    */
    public static function passport_decrypt($str,$key){ //解密函数
        $str=self::passport_key(base64_decode($str),$key);
        $tmp=  '';
        for($i=0;$i<strlen($str);$i++){
            $md5 =  $str[$i];
            $tmp .= $str[++$i] ^ $md5;
        }
        return $tmp;
    }
    /*
    *辅助函数
    */
    public static function passport_key($str,$encrypt_key){
        $encrypt_key=md5($encrypt_key);
        $ctr=0;
        $tmp='';
        for($i=0;$i<strlen($str);$i++){
            $ctr=$ctr==strlen($encrypt_key)?0:$ctr;
            $tmp.=$str[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

    /*
     *生成 n 位数的验证码
     */
    public static function createVerifyNum($count=4)
    {
        $num = [0,1,2,3,4,5,6,7,8,9];
        $verify = '' ;
        for ($i=0;$i<$count;$i++)
        {
            $verify .= array_rand($num) ;
        }

        return $verify;

    }


}