<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/2
 * Time: 16:03
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class MemberAddress extends ActiveRecord
{


    public function lister($mid)
    {
        return MemberAddress::find()
            ->where(['memberid'=>$mid])
            ->asArray()->all();
    }

    public function detail($id)
    {
        return MemberAddress::find()
            ->where(['id'=>$id])
            ->asArray()->one();
    }

}