<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/27
 * Time: 14:55
 */

namespace app\modules\components\helpers;


use yii\imagine\Image;

class MyImg
{


    public function getImg($url,$path,$quality=60)
    {
        $filename = pathinfo($url)['basename'];

        // 判断本地是否存在改文件
        if(file_exists('./img/lvyou/'.$path .'/'.$filename))
        return false;

        // 获得图片的大小
        $re = getimagesize($url);
        $width = $re['0'];
        $height =   $re['1'];

        // 若不存在则获得远程图片
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $imagedata = curl_exec($curl);
        curl_close($curl);

        // 保存远程图片到服务器
        $filePath = \Yii::getAlias('@webroot/img/' . $path . '/' .$filename);
        $tp = @fopen($filePath,'a');
        fwrite($tp,$imagedata);
        fclose($tp);
        // 再进行压缩
        $img = new Image();
        Image::thumbnail($url, $width, $height)
            ->save($filePath, ['quality' => $quality]);
        // 返回压缩后的图片
        return $filename;
    }

}