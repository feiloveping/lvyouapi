<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 18:22
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Question extends ActiveRecord
{
    public function getIndexQuestion()
    {
        return Question::find()
            ->select('id,content,replycontent')
            ->where('status=1 and questype=0')
            ->limit(3)
            ->asArray()->all();
    }

    public function getQuestionByTypeId($where)
    {
        $question = Question::find()
            ->select('content,replycontent,replytime,addtime,nickname,phone')
            ->where($where)
            ->asArray()
            ->all();
        if(!empty($question))
            foreach ($question as $k=>$v)
            {
                $question[$k]['replycontent'] = strip_tags($v['replycontent']);
            }
        return $question;
    }

    // 获得提问的列表
    public function getQuestionLister($typeid,$id,$page)
    {
        $query = Question::find()
            ->select('content,replycontent,replytime,addtime,nickname,phone')
            ->where(['status'=>1]);

        if($typeid != 0)  $query->andWhere(['typeid'=>$typeid,'productid'=>$id]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();

        $questionArr['pagecount']     =       $pagecount;
        if($page > $pagecount)
            return false;
        $question  = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy('addtime desc')
            ->asArray()
            ->all();

        if(!empty($question))
            foreach ($question as $k=>$v)
            {
                $question[$k]['replycontent'] = strip_tags($v['replycontent']);
            }

        $questionArr['question'] = $question;
        return $questionArr;
    }

    public function insertQuestion($data)
    {
        return  \Yii::$app->db->createCommand()->insert(Question::tableName(),$data)->execute();
    }

}