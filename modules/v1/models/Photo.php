<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 17:41
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Photo extends ActiveRecord
{

    public function getIndexPhoto()
    {
        $app_url = \Yii::$app->params['app_url'];

        return Photo::find()
            ->select('id,title,concat(\'' .$app_url. '\',`litpic`) as litpic,shownum,favorite')
            ->where('ishidden=0 and litpic is not null')
            ->orderBy('shownum desc')
            ->limit(6)
            ->asArray()->all();
    }

}