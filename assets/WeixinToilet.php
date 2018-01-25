<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/24
 * Time: 17:13
 */

namespace app\assets;

use yii\web\AssetBundle;

class WeixinToilet extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/weixintoilet/common.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/weixintoilet/common.js',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];

}