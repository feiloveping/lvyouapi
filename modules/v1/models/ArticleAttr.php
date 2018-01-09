<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/3
 * Time: 10:21
 */

namespace app\modules\v1\models;


use app\modules\components\helpers\CateTree;
use yii\db\ActiveRecord;

class ArticleAttr extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%article_attr}}';
    }

    public function getAttr()
    {
        $articel = ArticleAttr::find()
            ->select('id,attrname,pid')
            ->where(['isopen'=>1])
            ->orderBy('displayorder asc')
            ->asArray()
            ->all();
        $cateTree = new CateTree();
        return $cateTree->make_tree1($articel);

    }

}