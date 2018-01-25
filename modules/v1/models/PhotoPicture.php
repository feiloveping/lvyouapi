<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/22
 * Time: 14:36
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class PhotoPicture extends ActiveRecord
{

    public function getCountByPhotoId($id)
    {
        return PhotoPicture::find()
            ->where(['pid'=>$id])
            ->count();
    }

    // 根据图集id获得图片列表
    public function Lister($id)
    {
        return PhotoPicture::find()
            ->select('litpic')
            ->where(['pid'=>$id])
            ->asArray()
            ->all();
    }

}