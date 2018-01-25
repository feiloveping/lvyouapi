<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/8
 * Time: 17:54
 */

namespace app\modules\v1\controllers;


use app\modules\v1\models\Article;
use app\modules\v1\models\Line;
use app\modules\v1\models\LineJieshao;
use app\modules\v1\models\LineSuit;
use app\modules\v1\models\Notes;
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

    // 统一格式化字符串 - 短字符串
    public function actionInitStrShort()
    {
        $strings = \Yii::$app->request->get('mystrings');
        $data = ['strings'=>$strings];
        return $this->render('initstringshort',$data);
    }


    // 统一格式化字符串 - 长字符串
    public function actionInitStrLong()
    {

        $request = \Yii::$app->request;
        $param = $request->get('param');
        $id =  $request->get('id');
        $api_url = \Yii::$app->params['api_url'];
        $app_url = \Yii::$app->params['app_url'];

        /**
         * 处理相关的信息
         * line_feeinclude - 费用包含
         * line_day_jieshao - 线路介绍
         */
        $lineObj = new Line();
        $line_day_Obj = new LineJieshao();
        switch ($param)
        {
            case 'line_jieshao':
                $re = $lineObj->lineDetail($id);
                $data = $re['jieshao'] . $re['jieshao'];
                break;
            case 'line_suit_des':
                $lineSuitObj = new LineSuit();
                $re = $lineSuitObj->getSuitBySuitId($id) ;
                $data = $re['description'];
                break;
            case 'line_feeinclude':
                $re= $lineObj->lineDetail($id);
                $data = $re['feeinclude'] . $re['feenotinclude'];
                break;
            case 'line_payment':
                $re= $lineObj->lineDetail($id);
                $data = $re['payment'];
                break;
            case 'line_day_jieshao':
                $re = $line_day_Obj->getJieshaoByIdDay($id);
                $data = $re['jieshao'] ;
                break;
            case 'articledetail':
                $articleObj = new Article();
                $re = $articleObj->getDetails($id);
                $data = $re['content'];
                break;
            case 'notesdetail':
                $notesObj = new Notes();
                $re = $notesObj->getDetails($id);
                $data = $re['content'];
                break;
        }

        $strings = str_replace('"/uploads/', '"' .$app_url . '/uploads/' ,$data);

        return $this->render('initstringlong',['strings'=>$strings]);
    }

}