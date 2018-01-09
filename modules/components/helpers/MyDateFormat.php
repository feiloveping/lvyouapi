<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 17:00
 */

namespace app\modules\components\helpers;


class MyDateFormat
{
    public static function getAfterDayHourmin($time)
    {
        $t=$time - time();
/*        $timedefault = [
            '31536000'=>'年',
            '2592000'=>'月',
            '604800'=>'周',
            '86400'=>'天',
            '3600'=>'时',
            '60'=>'分',
            '1'=>'秒'
        ];*/
        $day = floor($t / 86400) ;
        $hour = floor(( $t - $day * 86400 ) / 3600 ) ;
        $min = floor(($t - $day * 86400 - $hour * 3600) /60) ;
        return ['day'=>$day,'hour'=>$hour , 'min'=>$min];
    }

}