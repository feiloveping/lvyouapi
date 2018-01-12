<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/3
 * Time: 9:56
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\Advertise;
use app\modules\v1\models\Article;
use app\modules\v1\models\ArticleAttr;
use app\modules\v1\models\Comment;
use app\modules\v1\models\Destinations;
use function foo\func;

class ArticleController extends DefaultController
{
    private  $_articleId;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->_articleId    =   \Yii::$app->params['typeid']['article'];
    }

    // 攻略首页 最新和推荐
    public function actionArticleIndex()
    {
        $newArticle     =   Article::getNew();
        $recommend      =   Article::getRecommend();

        // 分别对封面图进行处理
        $app_url        =   \Yii::$app->params['app_url'];
        foreach ($newArticle as $key=>$item)
        {
            $newArticle[$key]['litpic']     =       $app_url . $item['litpic'];
        }
        foreach ($recommend as $key=>$item) {
            $recommend[$key]['litpic'] = $app_url . $item['litpic'];
        }

        // 处理banner图片
        $banner             =   Advertise::getArticleBanner();
        $banner['adsrc']    =   unserialize($banner['adsrc']);
        $banner['adname']   =   unserialize($banner['adname']);
        $banner['adlink']   =   unserialize($banner['adlink']);

        // 处理图片
        foreach ($banner['adsrc'] as $k=>$v)
        {
            $banner['adsrc'][$k]  =   $app_url . $v;
        }

        $data['banner']     =   $banner;
        $data['new']        =   $newArticle;
        $data['recommend']  =   $recommend;
        return ['code'=>200,'data'=>$data,'msg'=>'ok'];
    }


    // 排序列表  0-0-0,0  城市-排序-筛选
    public function actionArticleLister()
    {
        $request    =       \Yii::$app->request;
        $param      =       $request->get('param','0-0-0,0');
        $page       =       (int) $request->get('page',1);
        $keyword    =       htmlentities(strip_tags($request->get('keyword',null)));
        // 对参数进行判断
        $params      =       explode('-',$param);
        if(count($params) !== 3) return ['code'=>403,'msg'=>'参数错误','data'=>''];

        // 获取列表
        $article = Article::getLister($param,$page,$keyword);
        if(!$article){
            $article['article'] = [];
        }else{
            // 处理图片信息
            $app_url        =       \Yii::$app->params['app_url'];
            foreach ($article['article'] as $k=>$v)
            {
                $article['article'][$k]['litpic']  =   $app_url . $v['litpic'];
            }
        }

        return ['code'=>200,'msg'=>'ok','data'=>$article];

    }

    // 详情页信息
    public function actionArticleDetail()
    {
        $id     =       \Yii::$app->request->get('id',null);
        if(!$id) return ['code'=>403,'msg'=>"参数不能为空",'data'=>''];
        $article = Article::getDetails($id);
        if(empty($article)) return ['code'=>404,'msg'=>[],'data'=>''];

        $app_url    =   \Yii::$app->params['app_url'];
        // 根据id和typeid获得评论量
        $typeid         =   \Yii::$app->params['typeid']['article'];
        $commentcount   =   Comment::getCommentCountByTypeId($typeid,$id)['count'];
        $data = [
            'id'            =>      $article['id'],
            'title'         =>      $article['title'],
            'shownum'       =>      $article['shownum'],
            'modtime'       =>      $article['modtime'],
            'content'       =>      str_replace('/uploads/',$app_url . '/uploads/',$article['content']),
            'commentcount'  =>    $commentcount,
        ];

        // 增加详情的链接 - webview处理
        $api_url                =       \Yii::$app->params['api_url'];
        $data['content_url']    =      $api_url . '/v1/detail?type=articledetail&id=' . $data['id'];
        return ['code'=>200,'data'=>$data,'msg'=>'ok'];
    }

    // 城市选择
    public function actionCity()
    {
        //$city = Destinations::getTengchongCity();
        $cache = \Yii::$app->cache;
        $key    =   'article_city';

        // 保存并设置
        $city = $cache->getOrSet($key,function (){
            return Destinations::getTengchongCity();
        },3600);

        array_unshift($city,['id'=>0,'kindname'=>'全城','pinyin'=>'quancheng']);
        return $city;
    }

    // 综合排序
    public function actionOtherConditions()
    {
        return \Yii::$app->params['article_condition_other'];
    }

    // 条件筛选 id,pid   1,2 则id为2的情况,1,0 则id为1的情况
    public function actionScreenCondition()
    {
        $attrid = ArticleAttr::getAttr();

        // 处理二级中的全部
        foreach ($attrid as $k=>$v)
        {
            if(!isset($v['son'])) $attrid[$k]['son'] = [];
            $sonTop = ['id'=>0,'attrname'=>'全部','pid'=>$v['id']];
            array_unshift($attrid[$k]['son'],$sonTop);
        }
        // 添加全部
        $all = ['id'=>0,'attrname'=>'全部','pid'=>0,'son'=>[['id'=>0,'attrname'=>'全部','pid'=>0],]];
        array_unshift($attrid,$all);
        return $attrid;
    }


    // 攻略条件-综合
    public function actionArticleCondition()
    {
        $city       =       $this->runAction('city');
        $othercondition =   $this->runAction('other-conditions');
        $screencondition=   $this->runAction('screen-condition');
        $data       =       [
            'city'      =>  $city,
            'othercondition'    =>  $othercondition,
            'screencondition'   =>  $screencondition
        ];

        return ['code'=>200,'data'=>$data,'msg'=>'ok'];
    }
}

