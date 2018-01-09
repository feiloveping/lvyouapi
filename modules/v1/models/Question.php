<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 18:22
 */

namespace app\modules\v1\models;


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
    public function insertQuestion($data)
    {
        return  \Yii::$app->db->createCommand()->insert(Question::tableName(),$data)->execute();
    }

}