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

    public function getSuitByLineId($id)
    {
        return LineSuit::find()
            ->select('id,lineid,suitname,description,propgroup')
            ->where(['lineid'=>$id])
            ->asArray()
            ->all();
    }

}