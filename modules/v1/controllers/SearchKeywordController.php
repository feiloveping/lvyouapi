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