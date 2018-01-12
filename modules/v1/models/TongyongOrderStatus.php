<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/11
 * Time: 11:29
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class TongyongOrderStatus extends ActiveRecord
{
    public function getOrderStatus()
    {
        $orderStatus = TongyongOrderStatus::find()
            ->orderBy('displayorder')
            ->all();
        return $orderStatus;
    }
}