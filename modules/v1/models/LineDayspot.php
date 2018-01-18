<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/18
 * Time: 13:42
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class LineDayspot extends ActiveRecord
{
     public function getDayMessageByLineId($lineid,$day)
     {
         return LineDayspot::find()
             ->select(['title','spotid','litpic'])
             ->where(['lineid'=>$lineid])
             ->asArray()
             ->all();
     }
}