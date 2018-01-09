<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/4
 * Time: 16:45
 */

namespace app\modules\v1\controllers;


use app\modules\v1\models\SearchKeyword;

class SearchKeywordController extends DefaultController
{

    // 熱門搜索关键词 - 全局使用
    public function actionHotKeyword()
    {
        $cache      =       \Yii::$app->cache;
        if(!$cache->exists('hotSearchKeyWord'))
        {
            $hotkeyword = SearchKeyword::getKeyword();
            $cache->set('hotSearchKeyWord',$hotkeyword,3600);
        }

        $hotkeyword = $cache->get('hotSearchKeyWord');

        if(empty($hotkeyword))
            return ['code'=>404,'data'=>[],'msg'=>'未找到数据'];
        else
            return ['code'=>200,'data'=>$hotkeyword,'msg'=>'ok'];
    }



    // 对关键词进行添加和修改
    public function actionAdd($keyword)
    {
        // 先查是否存在,存在则更新数目,不存在则新增
        $keywordModel = SearchKeyword::find()->where(['keyword'=>$keyword])->one();
        if($keywordModel)
        {
            $keywordModel->keynumber    =   $keywordModel->keynumber + 1;
            $keywordModel->addtime      =   time();
            $keywordModel->save();
        }else{
            \Yii::$app->db->createCommand()
                ->insert(SearchKeyword::tableName(), [
                    'keyword' => $keyword,
                    'addtime' => time()
                ])
                ->execute();
        }

    }

}