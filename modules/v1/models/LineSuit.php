<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/18
 * Time: 14:29
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class LineSuit extends ActiveRecord
{

    // 根据线路id查找先关信息
    public function getSuitByLineId($id)
    {
        return LineSuit::find()
            ->select('id,lineid,suitname,description,propgroup')
            ->where(['lineid'=>$id])
            ->asArray()
            ->all();
    }

    // 根据suitid查找相关信息
    public function getSuitBySuitId($id)
    {
        return LineSuit::find()
            ->select('id,lineid,suitname,description,propgroup')
            ->where(['id'=>$id])
            ->asArray()
            ->one();
    }


}