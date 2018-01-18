<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/17
 * Time: 13:50
 */

namespace app\modules\v1\models;


use app\modules\components\helpers\CateTree;
use yii\db\ActiveRecord;

class LineAttr extends ActiveRecord
{
    // 獲得所有的屬性
    public function getAllAttr()
    {
        $hotel = LineAttr::find()
            ->select('id,pid,attrname')
            ->where('isopen=1')
            ->asArray()
            ->all();
        $cate = new CateTree();
        return $cate->make_tree1($hotel);
    }
}