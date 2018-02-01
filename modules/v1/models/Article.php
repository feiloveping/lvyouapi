<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 16:14
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Article extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%article}}';
    }

    // 首页下方的热门攻略
    public function getIndexArticle()
    {
        $app_url = \Yii::$app->params['app_url'];
        return Article::find()
            ->select('id,title,concat(\''.$app_url.'\',`litpic`) as litpic,modtime,shownum')
            ->where('ishidden=0 and litpic is not null')
            ->orderBy('shownum desc')
            ->asArray()
            ->limit(3)
            ->all();
    }

    // 首页上方的热门攻略
    public function getIndexArticleUp()
    {
        $app_url = \Yii::$app->params['app_url'];
        return Article::find()
            ->select('id,title,concat(\''.$app_url.'\',`litpic`) as litpic')
            ->where('ishidden=0 and litpic is not null')
            ->orderBy('downnum desc')
            ->asArray()
            ->limit(3)
            ->all();
    }

    // 根据参数获取文档列表信息
    public function getLister($param,$page,$keyword='')
    {
        $query = Article::find()->select('id,title,litpic,modtime,shownum')
            ->where(['ishidden'=>0]);

        // 对参数进行筛选
        $params = explode('-',$param);

        // 关键字搜索
        if(!empty($keyword)) $query->andWhere(['like','title',$keyword]);

        // 城市
        if($params[0] != 0) $query->andWhere(['finaldestid'=>$params[0]]);

        // 排序
        switch ($params[1])
        {
            case 0:
                $query->orderBy('id desc');
                break;
            case 1:
                $query->orderBy('shownum desc');
                break;
            case 2:
                $query->orderBy('modtime desc');
                break;
        }

        //属性筛选 0,0
        $screen     =       explode(',',$params[2]);
        if($screen[1] != 0) $query->andWhere("find_in_set($screen[1],attrid)");
        if($screen[0] != 0 && $screen[1] == 0) {
            // 获取所有的子分类,并且遍历得到所有的结果
            $query->andWhere("find_in_set($screen[0],attrid)");
        }

        // 分页
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $article['article'] = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $article['pagecount'] = $pagecount;
        return $article;

    }


    // 根据id获得单条记录
    public function getDetails($id)
    {
        return Article::find()
            ->select('id,title,shownum,content,litpic,modtime,summary,isoffical')
            ->where(['id'=>$id])
            ->asArray()
            ->one();
    }


    // 获取最新的5个攻略
    public function getNew()
    {
        $newArticle = Article::find()
            ->select('id,title,litpic,summary')
            ->where('ishidden=0')
            ->orderBy('id desc')
            ->limit(5)
            ->asArray()
            ->all();

        return $newArticle;
    }

    // 获取浏览量最高的8个记录
    public function getRecommend()
    {
        $recommendArticle = Article::find()
            ->select('id,title,litpic,shownum,modtime')
            ->where('ishidden=0')
            ->orderBy('shownum desc')
            ->limit(8)
            ->asArray()
            ->all();
        return $recommendArticle;
    }
}