<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 17:28
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Notes extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%notes}}';
    }

    public function getIndexNotes()
    {
        $app_url = \Yii::$app->params['app_url'];
        return Notes::find()
            ->select('id,title,concat(\'' .$app_url. '\',`litpic`) as litpic,shownum,modtime')
            ->where('status=1 and litpic is not null')
            ->orderBy('shownum desc')
            ->limit(3)
            ->asArray()
            ->all();

    }

    // 获取最新的5个游记
    public function getNew()
    {
        $newNotes = Notes::find()
            ->select('id,title,litpic,description')
            ->where('status=1')
            ->orderBy('modtime desc')
            ->limit(5)
            ->asArray()
            ->all();

        return $newNotes;
    }


    // 获得首页默认的8个游记
    public function getRecommend()
    {
        $notes  =   Notes::find()
            ->select('id,title,shownum,litpic,modtime')
            ->where('status=1')
            ->orderBy('shownum desc')
            ->limit(5)
            ->asArray()
            ->all();

        return $notes;
    }

    // 列表 - 带搜索
    public function getLister($page,$keyword)
    {
        $query  =   Notes::find()
            ->select('id,title,litpic,shownum,modtime')
            ->where('status=1');
        if($keyword) $query->andWhere(['like','title',$keyword]);

        // 分页
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $notes['notes'] = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $notes['pagecount'] = $pagecount;
        return $notes;


    }

    // 详情页
    public function getDetails($id)
    {
        $notes = Notes::find()
            ->select('n.id,n.title,m.nickname,n.shownum,n.modtime,n.content')
            ->alias('n')
            ->where(['id'=>$id])
            ->leftJoin(Member::tableName() . ' as m','n.memberid=m.mid')
            ->asArray()
            ->one();
        return $notes;
    }



}