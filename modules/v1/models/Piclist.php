<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/13
 * Time: 10:17
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

// 统一处理图集的数据模型类

class Piclist extends ActiveRecord
{
    public function getPicListById($type , $id)
    {
        switch ($type)
        {
            case 5:
                $picModel = Spot::find();
                break;
            case 2:
                $picModel = Hotel::find();
        }

        $pic = $picModel->select('title,litpic,piclist')->where('id='.$id)->asArray()->one();

        if(empty($pic))
            return [];
        else {
            // 对$pic进行图片处理
            $app_url = \Yii::$app->params['app_url'];
            $pic['litpic'] = $app_url . $pic['litpic'];
            $pic['piclist'] = explode(',', $pic['piclist']);
            foreach ($pic['piclist'] as $k => $v) {
                $pic['piclist'][$k] = $app_url . $v;
            }
            return $pic;
        }
    }

}