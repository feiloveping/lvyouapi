<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/2
 * Time: 11:56
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class SearchKeyword extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%search_keyword}}";
    }

    // 獲得熱門搜索
    public function getKeyword()
    {
        return SearchKeyword::find()
            ->select('keyword')
            ->orderBy('isopen desc , keynumber desc')
            ->limit(10)
            ->asArray()
            ->all();
    }
}