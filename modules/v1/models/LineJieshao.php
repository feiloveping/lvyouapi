<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/18
 * Time: 11:32
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class LineJieshao extends ActiveRecord
{

    // 获得景点的每天介绍
    public function getJieshaoById($id)
    {
        return LineJieshao::find()
            ->where(['lineid'=>$id])
            ->asArray()
            ->orderBy('day')
            ->all();
    }

    // 根据介绍id获得介绍的信息
    public function getJieshaoByIdDay($id)
    {
        return LineJieshao::find()
            ->select('id,jieshao')
            ->where(['id'=>$id])
            ->asArray()
            ->one();
    }
}