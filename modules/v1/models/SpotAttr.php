<?php

namespace app\modules\v1\models;

use app\modules\components\helpers\CateTree;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property string $name
 */
class SpotAttr extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spot_attr}}';
    }

    public function getAttr()
    {
        $spotAttr = SpotAttr::find()
            ->select('id,attrname')
            ->orderBy('id')
            ->limit('6')
            ->asArray()
            ->all();

        // 找到所有的属性，再统计相应属性下景点的销量
            foreach ($spotAttr as $k => $item) {
                $spotAttr[$k]['bookcount'] = Spot::getBookCountByAttrId($item['id']);
            }

        return $spotAttr;
    }


    // 获得所有的顶级属性
    public function getAttrAll()
    {
        $spotattr = SpotAttr::find()
            ->select('id,attrname,pid')
            ->where('isopen=1')
            ->orderBy('displayorder')
            ->asArray()->all();
        $tree = new CateTree();
        return  $tree->make_tree1($spotattr);
    }

    // 根据顶级属性获得子属性
    public function getSonAttr($id)
    {
        $spotattr = SpotAttr::find()
            ->select('id,attrname,pid')
            ->where('isopen=1 and pid='.$id)
            ->orderBy('displayorder')
            ->asArray()->all();
        return $spotattr;
    }

}
