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
        $today = strtotime(date("Y-m-d",time()));

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
                $dv['mydays'] = date('Y-m-d',$dv['day']);
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

    public function initTwoDate($time)
    {
        // 获取前两天的数据
        // 对过期套餐/门票进行过滤 - 选择前两天的数据(若想要全部数据则不进行处理)
        $nowtime = strtotime(date("Y-m-d",time()));
        $tomorrow = $nowtime + 86400;
        foreach ($time as $k2=>$v2)
        {
            $v2['mydays'] = date('Y-m-d',$v2['day']);
            if($v2['day'] >= $nowtime) {
                $times[] = $v2;
                if(count($times) > 1 ) break ;
            }
        }

        // 对当前套餐/门票 进行日期过滤 , 若这两天无数据.则进行置空 (前段要求)
        if($times[0]['day'] != $nowtime)
            $times[0] = [
                "day"=> $nowtime,
                "adultprice"=> "",
                "number"=> "0",
                "mydays"=> date('Y-m-d',$nowtime) ,
            ];

        if($times[0]['day'] == $tomorrow){
            $times[1] = $times[0];
            $times[0] = [
                "day"=> $nowtime,
                "adultprice"=> "",
                "number"=> "0",
                "mydays"=> date('Y-m-d',$nowtime) ,
            ];
        }

        if($times[1]['day'] != $tomorrow)
            $times[1] = [
                "day"=> $tomorrow,
                "adultprice"=> "",
                "number"=> "0",
                "mydays"=> date('Y-m-d',$tomorrow) ,
            ];

        return $times;
    }

    public function getAgeByBirthDay($birthday)
    {
        $year = date('Y',time());
        if(strpos($birthday,'/'))
            $birthYear = explode('/',$birthday)[0];
        elseif (strpos($birthday,'-'))
            $birthYear = explode('/',$birthday)[0];

        if($birthYear < 1900 )
            return false;
        return $year - $birthYear;
    }

}