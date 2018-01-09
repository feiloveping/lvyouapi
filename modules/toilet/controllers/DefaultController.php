<?php

namespace app\modules\toilet\controllers;

use app\modules\components\helpers\MyImg;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\Controller;

/**
 * Default controller for the `toilet` module
 */
class DefaultController extends Controller
{
    /**
     * 缩略图
     * Renders the index view for the module
     * @return string
     */

    public function actionIndex()
    {

        $img = "http://lvyou.wmqt.net/uploads/toilet/20171220wc0001.jpg";

        $img = new Image();
        Image::thumbnail($img, 120, 120)
            ->save(\Yii::getAlias('@webroot/img/thumb-test-image.jpg'), ['quality' => 100]);


        exit;
        return $this->render('index');
    }

    // 下载图片到本地

    public function actionGetimg()
    {


        $url = \Yii::$app->request->get('url',"http://lvyou.wmqt.net/uploads/toilet/20171220wc0001.jpg");
        $myImg = new MyImg();
        $filename = $myImg->getImg($url,'lvyou','50');

//        \URL::to('@web/images/logo.gif', true);
//        return $filename;
    }

    function GrabImage($url, $filename = "") {
        if ($url == ""):return false;
        endif;
        //如果$url地址为空，直接退出
        if ($filename == "") {
            //如果没有指定新的文件名
            $ext = strrchr($url, ".");
            //得到$url的图片格式
            if ($ext != ".gif" || $ext != ".jpg" || $ext != ".png"):return false;
            endif;
            //如果图片格式不为.gif或者.jpg，直接退出
            $filename = date("dMYHis") . $ext;
            //用天月面时分秒来命名新的文件名
        }
        ob_start();//打开输出
        readfile($url);//输出图片文件
        $img = ob_get_contents();//得到浏览器输出
        ob_end_clean();//清除输出并关闭
        $size = strlen($img);//得到图片大小
        $fp2 = @fopen($filename, "a");
        fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
        fclose($fp2);
        return $filename;//返回新的文件名
    }
    //裁剪
    public function actionCrop()
    {
        return Image::crop('@webroot/img/a.png', 100, 100,[1000,1000])
            ->save(\Yii::getAlias('@webroot/img/crop-test-image.jpg'), ['quality' => 100]);
    }

    //旋转
    public function actionRotate()
    {
        //旋转
        Image::frame('@webroot/img/a.png', 15, '666', 2)
            ->rotate(-18)
            ->save(\Yii::getAlias('@webroot/img/rotate-test-frame.jpg'), ['quality' => 100]);
    }

    // 水印

    public function actionWater()
    {
        //水印
        Image::watermark('@webroot/img/a.png', '@webroot/img/water.png', [500,500])
            ->save(\Yii::getAlias('@webroot/img/water-test-frame.jpg'), ['quality' => 100]);
    }

    // 文字水印

    public function actionTxt(){

        echo \Yii::getAlias('@common');

        exit;

        Image::text('@webroot/img/a.png', 'hello world', '@webroot/img/langye.ttf',[100,100],['color'=>'aabbcc','size'=>150])
            ->save(\Yii::getAlias('@webroot/img/thumb-test-text.jpg'), ['quality' => 100]);
    }



}
