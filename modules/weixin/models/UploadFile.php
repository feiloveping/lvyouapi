<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/31
 * Time: 15:57
 */

use Yii\base\Model;
use yii\web\UploadedFile;
class UploadFile extends Model
{
    public $imageFiles;
    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 4],
        ];
    }
    public function upload()
    {
        if ($this->validate()) {
            foreach ($this->imageFiles as $file) {
                $file->saveAs('./img/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }



}