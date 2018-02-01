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
        $filePath = './img/lvyou/'.$path .'/'.$filename;
        if(file_exists($filePath))
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
        $tp = @fopen($filePath,'w+');
        fwrite($tp,$imagedata);
        fclose($tp);
        // 再进行压缩
        $img = new Image();
        Image::thumbnail($url, $width, $height)
            ->save($filePath, ['quality' => $quality]);
        // 返回压缩后的图片
        return $filename;
    }


    //
    public function uploadImgBy64($base64_image_content,$path,$api_url)
    {
        header('Content-type:text/html;charset=utf-8');
        if(!$base64_image_content)
            return false;

        // 匹配base是否带img
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $base64_strings = str_replace($result[1], '', $base64_image_content);

        }else{
            $type = 'png';
            $base64_strings = $base64_image_content;
        }
        $new_file = $path .date('Ymd',time())."/";
        if(!file_exists($new_file))
        {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0700);
        }
        $new_file = $new_file.time().rand(1000,9999).".{$type}";
        if (file_put_contents($new_file, base64_decode($base64_strings))){
            return $api_url . trim($new_file,'.' );
        }else
            return false;


//        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
//            $type = $result[2];
//            $new_file = $path .date('Ymd',time())."/";
//            if(!file_exists($new_file))
//            {
//                //检查是否有该文件夹，如果没有就创建，并给予最高权限
//                mkdir($new_file, 0700);
//            }
//            $new_file = $new_file.time().rand(1000,9999).".{$type}";
//            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
//                return $api_url . trim($new_file,'.' );
//            }else
//                return false;
//        }
    }

}