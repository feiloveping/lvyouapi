<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/8
 * Time: 17:54
 */

namespace app\modules\v1\controllers;


use Gregwar\Captcha\CaptchaBuilder;
use yii\web\Controller;

class ResourceController extends Controller
{
    public $layout = false;
    // 获得详情页的内容
    public function actionDetails()
    {
        return $this->render('detail');
    }

    // 生成验证码
    public function actionCreateVerify()
    {
        header('Content-type: image/jpeg');
        $builder = new CaptchaBuilder();
        $builder->build();
        header('Content-type:image/jpeg');
        $builder->output();
    }

    public function actionTest()
    {
        return $this->render('test');
    }


}