<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/2
 * Time: 14:25
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Suggest extends ActiveRecord
{


    // 统计每分钟该用户提交的建议总数
    public function getSuggestCountOneMin($mid)
    {
        return Suggest::find()->where(['<','createtime',time()-60])
            ->andWhere(['mid'=>$mid])
            ->count();
    }
}