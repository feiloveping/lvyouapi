<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/8
 * Time: 18:02
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class MemberOrderTourer extends ActiveRecord
{
    public function getTourByOrderId($id)
    {
        return MemberOrderTourer::find()
            ->select('id,tourername,cardtype,cardnumber')
            ->where(['orderid'=>$id])
            ->asArray()
            ->all();
    }
}