<?php

namespace app\modules\v1\controllers;

use app\modules\components\helpers\FeiToken;
use app\modules\components\helpers\MyEncrypt;
use app\modules\v1\models\Member;
use app\modules\v1\models\SearchKeyword;
use yii\rest\ActiveController;
use yii\web\Response;


class DefaultController extends ActiveController
{
    public $modelClass = '' ;
    public $token;
    public $logsign = false ;
    public $mid ;
    public $key;
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }


    public function init()
    {
        parent::init();
        $this->key = \Yii::$app->params['myEncrypt_key'] ;
        $header = \Yii::$app->request->headers;
        $this->token = $header->get('token');
        if( $this->token && Feitoken::checkToken($this->token))
        {
            $members = explode(':',$this->getMember());
            $mid = $members[1] ;
            // 对memberid真伪性做判断
            if(!empty(Member::getMember(['mid'=>$mid])))
            {
                $this->logsign = true ;
                $this->mid = $mid ;
            }
        }
    }

    public function getToken()
    {

    }

    public function getMember()
    {
        return MyEncrypt::passport_decrypt($this->token,$this->key);
    }


}
