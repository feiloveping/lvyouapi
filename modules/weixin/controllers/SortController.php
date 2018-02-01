<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/30
 * Time: 10:09
 */

namespace app\modules\weixin\controllers;


use yii\web\Controller;

class SortController extends Controller
{

    /**
     * @param $arr 要排序的数组参数
     */
    public function fastSort($arr)
    {
        $length = count($arr);
        if($length <= 1)
            return $arr;

        $base_num = $arr[0];
        $left_arr = [];
        $right_arr = [];
        for ($i=1;$i<$length;$i++)
        {
            if($base_num < $arr[$i])
                $left_arr[] = $arr[$i];
            else
                $right_arr[] = $arr[$i];
        }

        $left_arr = self::fastSort($left_arr);
        $right_arr = self::fastSort($right_arr);
        return array_merge($left_arr,[$base_num],$right_arr);
    }
    public function actionFastSort()
    {
        $arr =[1,3,5,7,32,2,54,63,563,56,34634,6345,63456,1];
        $RE = $this->fastSort($arr);
        var_dump($RE);
    }

    /**
     * 冒泡排序
     */
    public function bubbleSort($arr)
    {
        $length = count($arr);
        for ($i=0;$i<$length-1;$i++)
        {
            for ($j=$i+1;$j<$length;$j++)
            {
                if($arr[$i]>$arr[$j])
                {
                    $t = $arr[$i];
                    $arr[$i] = $arr[$j];
                    $arr[$j] = $t;
                }
            }
        }

        return $arr;
    }
    public function actionBubbleSort()
    {
        $arr =[1,3,5,7,32,2,54,63,563,56,34634,6345,63456,1];
        $RE = $this->bubbleSort($arr);
        var_dump($RE);
    }



    public function insertSort($arr)
    {
       $length = count($arr);
       for ($i=1;$i<$length;$i++)
       {
           $tmp = $arr[$i];
           for ($j=$i-1;$j>=0;$j--)
           {
               if($tmp < $arr[$j])
               {
                   $tmp = $arr[$j];

                   $arr[$j] = $arr[$j+1];

               }
           }
       }

       return $arr;
    }


    public function actionInsertSort()
    {
        $arr =[1,3,5,7,32,2,54,63,563,56,34634,6345,63456,1];
        $RE = $this->insertSort($arr);
        var_dump($RE);
    }



}