<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/10
 * Time: 18:05
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Line extends ActiveRecord
{
    //根据id选择简单的几个数据
    public function lineEasyDetail($id)
    {
        $line      =       Line::find()
            ->select('id,aid,supplierlist,title')
            ->where('id=' . $id )
            ->asArray()
            ->one();
        return $line;
    }

    // 根据参数获得数据 - 列表
    public function listerPyParam($param , $page ,$keyword = '')
    {
        $query      =       Line::find()
            ->alias('l')
            ->select('l.id,l.title,l.price,l.price,l.bookcount,l.satisfyscore,l.litpic,l.iconlist')
            ->where(['l.ishidden'=>0])
            ->innerJoin(LineSuit::tableName() . ' as ls','ls.lineid=l.id');
        // 搜索
        if(!empty($keyword))
            $query->andWhere(['like','l.title',$keyword]);

        $params     =       explode('-',$param);
        // 处理第一个参数 目的地
        if($params[0])
            $query->andWhere(['l.startcity'=>$params[0]]);
        // 处理第二个参数 排序
        switch ($params[1])
        {
            case 0:
                break;
            case 1:
                $query->orderBy('l.shownum desc');
                break;
            case 2:
                $query->orderBy('l.price');
                break;
            case 3:
                $query->orderBy('l.price desc');
                break;
            case 4:
                $query->orderBy('l.bookcount desc');
                break;
        }

        // 处理第三个参数 属性 pid,id
        $lineAttr = explode(',',$params[2]);
        if($lineAttr[0] != 0 )
        {
            if($lineAttr[1] == 0)
            {
                $query->andWhere("find_in_set($lineAttr[0],l.attrid)");
            }
            if($lineAttr[1]!= 0){
                $query->andWhere("find_in_set($lineAttr[1],l.attrid)");
            }
        }
        //  分页
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $lineArr = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $line['pagecount'] = $pagecount;
        $line['line'] = $lineArr ;

        return $line;

    }

    // 根据获得详细数据
    public function lineDetail($id)
    {
        $line      =       Line::find()
            ->select('id,title,sellpoint,bookcount,satisfyscore,price,lineday,piclist,reserved1 as jieshao,
                            storeprice,supplierlist,title,features ,feeinclude,payment'
                )
            ->where('id=' . $id )
            ->asArray()
            ->one();
        return $line;
    }

    // 根据lineid获得一些简单信息
    public function getEasyLineByid($id)
    {
        $line      =       Line::find()
            ->select('id,aid,title,litpic,supplierlist,title,linebefore,storeprice')
            ->where(['id'=>$id] )
            ->asArray()
            ->one();
        return $line;
    }

    // 获得首页的简单列表
    public function getListerHome()
    {
        return Line::find()
            ->select('l.id,l.litpic,l.title,l.sellpoint')
            ->alias('l')
            ->where(['l.ishidden'=>0])
            ->orderBy('l.bookcount desc')
            ->innerJoin(LineSuit::tableName() . ' as ls','ls.lineid=l.id')
            ->limit(6)
            ->asArray()->all();
    }

    // 根据id获取收藏所需要的信息
    public function collectionMessage($id)
    {
        return Line::find()
            ->select('id as indexid,price,litpic,bookcount,title,iconlist,satisfyscore')
            ->where('id='.$id)
            ->asArray()->one();
    }


}