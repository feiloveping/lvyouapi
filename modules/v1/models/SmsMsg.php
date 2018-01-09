<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/12
 * Time: 14:23
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class SmsMsg extends ActiveRecord
{
    public function getMsgByType($msgtype)
    {
        return SmsMsg::find()
            ->select('msg')
            ->where('isopen=1 and msgtype=\''.$msgtype . '\'')
            ->one();
    }

}