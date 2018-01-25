<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/22
 * Time: 13:50
 */

namespace app\modules\v1\models;


use app\modules\components\helpers\CateTree;
use yii\db\ActiveRecord;

class PhotoAttr extends ActiveRecord
{

    public function getAllAttr()
    {
        $attr =  PhotoAttr::find()
            ->select('id,attrname,pid')
            ->where(['isopen'=>1])
            ->asArray()
            ->all();
        // 格式化
        $tree = new CateTree();
        return  $tree->make_tree1($attr);
    }
}