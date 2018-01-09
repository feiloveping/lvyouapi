<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/8
 * Time: 17:54
 */

namespace app\modules\v1\controllers;


use yii\web\Controller;

class ResourceController extends Controller
{
    public $layout = false;
    // 获得详情页的内容
    public function actionDetails()
    {
        return $this->render('detail');
    }


}