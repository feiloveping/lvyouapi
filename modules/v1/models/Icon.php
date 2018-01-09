<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/2
 * Time: 11:19
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Icon extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%icon}}";
    }

    public function getIconNameByIds($iconid)
    {
        // select kind as iconname from sline_icon where find_in_set(id,'$v[iconlist]') limit 3
        return Icon::find()
            ->select('kind')
            ->where(['id'=>$iconid])
            ->asArray()
            ->one();
    }

    // 根据iconlists获得最多两个的iconname
    public function getIconTwoNameByIds($iconids)
    {
        return Icon::find()
            ->select('kind as iconname')
            ->where(['id'=>$iconids])
            ->asArray()
            ->limit(2)
            ->all();
    }


}