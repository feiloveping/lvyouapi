<?php

namespace app\modules\v1\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%goods}}".
 *  景區 目的地 表
 * @property integer $id
 * @property string $name
 */
class Destinations extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%destinations}}';
    }


    public function fields() {
        return [

        ];
    }
    public function extraFields() {

    }

    // 獲得排序最高的目的地列表
    public function actionDestinationByOrder()
    {
        return Destinations::find()
            ->orderBy('displayorder asc')
            ->where(['isopen'=>1])
            ->asArray()
            ->limit(6)
            ->all();
    }


    // 获得腾冲下面的所有地址 腾冲pid为53

    public function getTengchongCity()
    {
        return Destinations::find()
            ->select(['id','kindname','pinyin'])
            ->where(['pid'=>53,'isopen'=>1])
            ->asArray()->all();
    }



}
