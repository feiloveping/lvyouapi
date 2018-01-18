<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/18
 * Time: 15:18
 */

namespace app\modules\v1\controllers;


use app\modules\v1\models\Member;
use app\modules\v1\models\Question;

class QuestionController extends DefaultController
{
    // 问答发布 - 通用
    public function actionAdd()
    {
        if(!$this->logsign)
            return ['code'=>401,'msg'=>'用户未登录','data'=>''];

        $reqest         = \Yii::$app->request;
        $content        = htmlentities(strip_tags($reqest->post('content',0)));
        $typeid         = $reqest->post('typeid');
        $id             = $reqest->post('id');
        $isanonymous    = $reqest->post('isanonymous',0); // 1 匿名    0 不匿名
        $ip             = $reqest->getUserIP();
        if(mb_strlen($content) < 5 ) return ['code'=>404,'msg'=>'请至少输入5个字','data'=>''];
        // 验证typeid的真伪
        $member         = $this->actionGetNickname() ;
        $mobile         = $member['mobile'] ;
        $nickname       = $member['nickname'] ;
        if($isanonymous == 1)
            $nickname = '匿名';
        $data = [
            'typeid'        =>      $typeid,
            'productid'     =>      $id,
            'content'       =>      $content,
            'nickname'      =>      $nickname,
            'ip'            =>      $ip,
            'memberid'      =>      $this->mid,
            'addtime'       =>      time(),
            'phone'         =>      $mobile,
        ] ;

        // 新增酒店的问答
        $re = Question::insertQuestion($data);
        if($re != false)
            return ['code'=>200 , 'msg'=>'ok' ,'data'=>''];
        else
            return ['code'=>403 , 'msg'=>'新增失败' ,'data'=>''];
    }

    // 问答列表
    public function actionLister()
    {
        $request        = \Yii::$app->request;
        $id             = $request->get('id',-1);
        $typeid         = $request->get('typeid');
        $where          = [
            'typeid'    =>  $typeid,
            'productid' =>  $id,
        ];
        $question = Question::getQuestionByTypeId($where);
        if(empty($question))
            return ['code'=>404 , 'msg'=>'未找到数据','data'=>''];
        else
            return ['code'=>200 , 'msg'=>'ok','data'=>$question] ;

    }


    // 是否登录-若登录获取昵称
    public function actionGetNickname()
    {
        if($this->logsign){
            // 根据用户id拿到用户昵称
            return Member::getMember(['mid'=>$this->mid]);
        }else
            return false ;
    }
}