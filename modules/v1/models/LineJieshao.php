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

    public function getJieshaoById($id)
    {
        return LineJieshao::find()
            ->where(['id'=>$id])
            ->asArray()
            ->orderBy('day')
            ->all();
    }

}