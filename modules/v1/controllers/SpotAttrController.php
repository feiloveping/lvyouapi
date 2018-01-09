<?php

namespace app\modules\v1\controllers;


use app\modules\v1\models\SpotAttr;
use yii\rest\ActiveController;
use yii\web\Response;

class SpotAttrController extends ActiveController
{
    public $modelClass = 'app\modules\v1\models\spotAttr';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;

    }


    public function actions() {
        $actions = parent::actions();
        // 禁用""index,delete" 和 "create" 操作
        unset($actions['index'],$actions['delete'], $actions['create']);
        return $actions;
    }

    // 首页 景点属性列表（名称-预定量）
    public function actionSpotAttr()
    {
        $spot = SpotAttr::getAttr();
        if(!$spot) return ['code'=>404,'msg'=>'未找到数据'];
        return ['code'=>200,'data'=>$spot];

    }


}
