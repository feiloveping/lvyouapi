<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/10
 * Time: 18:05
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Line extends ActiveRecord
{
    //根据id选择简单的几个数据
    public function lineEasyDetail($id)
    {
        $line      =       Tuan::find()
            ->select('id,aid,supplierlist,title')
            ->where('id=' . $id )
            ->asArray()
            ->one();
        return $line;
    }

}