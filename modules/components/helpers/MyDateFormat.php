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

    public function getThreeMonthDate()
    {
        // 当前时间凌晨
        $today = strtotime(date("Y-m-d"));

        // 三月后的时间戳
        $afterThreeMonth = strtotime('+3 month');
        $data = [];
        for ($i = $today;$i<$afterThreeMonth;$i = $i + 86400)
        {
            $data[] = $i;
        }

        return $data;
    }

    public function initDate($data,$key)
    {

        $alltime = $this->getThreeMonthDate();
        // 格式化数据 拿到所需要的价格
        foreach ($alltime as $k=>$v)
        {
            foreach ($data as $dk=>$dv)
            {
                if($dv[$key] == $v)
                {
                    $alltime[$k] = $dv;
                }
            }
        }
        // 格式化数据 统一数据格式
        foreach ($alltime as $k=>$v)
        {
            if(!is_array($alltime[$k])){
                unset($alltime[$k]);
                $alltime[$k][$key] = $v;
            }

        }
        return $alltime;
    }

}