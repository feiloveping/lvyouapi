<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/22
 * Time: 15:22
 */

namespace app\assets;


use yii\web\AssetBundle;

class InitString extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/initstring/base.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/initstring/lib.js',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];


}