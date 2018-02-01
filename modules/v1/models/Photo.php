<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 17:41
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Photo extends ActiveRecord
{

    public function getIndexPhoto()
    {
        $app_url = \Yii::$app->params['app_url'];

        return Photo::find()
            ->select('id,title,concat(\'' .$app_url. '\',`litpic`) as litpic,shownum,favorite')
            ->where('ishidden=0 and litpic is not null')
            ->orderBy('shownum desc')
            ->limit(6)
            ->asArray()->all();
    }

    // 图集列表
    public function Lister($param,$page,$keyword='')
    {
        $params = explode('-',$param);

        $query = Photo::find()
            ->select('id,title,litpic,content,favorite,')
            ->where(['ishidden'=>0]);

        //对搜索处理
        if(!empty($keyword))
            $query->andWhere(['like','title',$keyword]);

        // 对目的地进行筛选
        if($params[0] != 0)
            $query->andWhere(['finaldestid'=>$params[0]]);

        // 对排序进行判断
        if($params[1] != 0)
            $query->orderBy('shownum desc');

        // 对属性进行判断 - 先不管,可以使用分组形式
        $attr = explode(',',$params[2]);

        // 分页
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $photo['photo'] = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $photo['pagecount'] = $pagecount;

        return $photo;
    }

    // 图集详情
    public function Detail($id)
    {
        return Photo::find()
            ->select('id,title,favorite')
            ->where(['ishidden'=>0,'id'=>$id])
            ->asArray()
            ->one();
    }



}