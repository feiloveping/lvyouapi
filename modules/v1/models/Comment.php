<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 15:31
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Comment extends ActiveRecord
{

    // 根据typeid 和 articleid 获得评价总数
    public function getCommentCountByTypeId($typeid,$articleid)
    {
        return Comment::find()
            ->where(['typeid'=>$typeid,'articleid'=>$articleid])
            ->andWhere(['isshow'=>1])
            ->asArray()
            ->count();
    }
    // 获得有图片的评论总数

    public function getCommentHasImg($typeid,$articleid)
    {
        return Comment::find()
            ->where('typeid=' . $typeid .' and articleid= '.$articleid)
            ->andWhere(['isshow'=>1])
            ->andWhere('piclist is not null')
            ->asArray()
            ->count();
    }

    // 根据typeid和articleid获得评价详情
    public function getCommentStarCount($typeid,$articleid)
    {

        $common =  Comment::find($typeid,$articleid)
            ->select('level,count(*) as commentcount')
            ->where(['typeid'=>$typeid,'articleid'=>$articleid])
            ->andWhere(['isshow'=>1])
            ->groupBy('level')
            ->asArray()
            ->all();
        $array  =   [0,1,2,3,4,5];
        foreach ($common as $k=>$v)
        {
            if(in_array($v['level'],$array))
            {
                $array[$v['level']]     =       [
                    'level'     =>      $v['level'],
                    'commentcount'=>     $v['commentcount'],
                ];
            }
        }
        foreach ($array as $k=>$v)
        {
            if(count($v) < 2)
            {
                $array[$k]  =   [
                    'level' =>$k,
                    'commentcount'=>0
                ];
            }
        }
        return $array;

    }

    // 对星级进行转化成好评级别 1星差评,2-3中评,4-5好评
    public function getLevelComment(array $common)
    {
        $bad        =       $common[1]['commentcount'];
        $good       =       0;
        $verygood   =       0;

        foreach ($common as $k=>$v)
        {
            if($k==2 || $k==3) $good += $v['commentcount'];
            if($k==4 || $k==5) $verygood += $v['commentcount'];
        }

        return ['bad'=>$bad,'good'=>$good,'verygood'=>$verygood];

    }

    // 根据条件进行评论分页列表
    public function getCommentByPage($typeid,$id,$page)
    {
        $query      =   Comment::find()
            ->alias('c')
            ->select('c.content,c.addtime,c.piclist,c.level as star,c.vr_nickname , m.nickname,c.vr_grade ,m.rank')
            ->where('c.isshow=1 and c.typeid= '.$typeid.' and c.articleid='.$id);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];

        $pagecount = $pages->getPageCount();

        $hotelcomment['pagecount']     =       $pagecount;
        if($page > $pagecount)
            return false;
        $hotelcomment['commentlist'] = $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->leftJoin(Member::tableName() . 'as m' ,'m.virtual=1 and m.mid=c.memberid')
            ->asArray()
            ->all();

        return $hotelcomment;
    }

    // 全部 0 ,好评 1 , 中评 2, 差评 3, 有图 4     1星差评,2-3中评,4-5好评
    // 根据条件进行分页列表-含有嵌套关系
    public function getCommentByPageLevel($typeid,$id,$page , $level='')
    {
        $query      =   Comment::find()
            ->alias('c')
            ->select('c.id,c.memberid,c.content,c.vr_headpic,c.addtime,c.dockid,c.level as star,c.vr_nickname ,
             m.nickname,c.vr_grade , c.piclist ,m.rank ,m.litpic as headpic ')
            ->orderBy('c.id desc')
            ->where('c.isshow=1 ');

        if($typeid != 0)
            $query->andWhere('c.typeid= '.$typeid);

        if($id != 0)
            $query->andWhere('c.articleid='.$id);

        if($level == 1)
            $query->andWhere(['or','level=4','level=5']);
        elseif($level == 2)
            $query->andWhere(['or','level=2','level=3']);
        elseif($level == 3)
            $query->andWhere(['level'=>1]);
        elseif ($level ==4)
            $query->andWhere(['not', ['litpic' => null]]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];

        $pagecount = $pages->getPageCount();
        $articlecomment['pagecount']     =       $pagecount;

        if($page > $pagecount)
            return false;
        $commentlist  = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->leftJoin(Member::tableName() . 'as m' ,'c.memberid=m.mid')
            ->asArray()
            ->all();

        // 处理被回复关系 - 先格式化数据
        foreach ($commentlist as $v)
        {
            $commentlistInit[$v['id']] = $v;
        }

        foreach ($commentlistInit as $k=>$v)
        {
            if($v['dockid'] == 0){
                $replyname = '';
            }else{
                // 对回复者的身份进行判断
                if(! $commentlistInit[$v['dockid']]['memberid'])
                    $replyname = $commentlistInit[$v['dockid']]['vr_nickname'];
                else
                    $replyname = $commentlistInit[$v['dockid']]['nickname'];

                if(!$replyname) $replyname = '匿名用户' ;
            }
            $commentlistInit[$k]['replyname'] = $replyname;
        }

        $articlecomment['commentlist'] = array_values($commentlistInit);
        return $articlecomment;
    }

}