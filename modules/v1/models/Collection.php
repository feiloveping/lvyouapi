<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 18:04
 */

namespace app\modules\v1\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Collection extends ActiveRecord
{
    public function addCollection()
    {
        return 'add';
    }

    // 判断是否存在收藏表中
    public function isCollection($typeid,$indexid,$memberid)
    {
        return Collection::find()->where(['typeid'=>$typeid,'indexid'=>$indexid,'memberid'=>$memberid])->exists();
    }

    // 删除收藏
    public function delCollection($typeid,$indexid,$mid)
    {
        return Collection::find()->where(['typeid'=>$typeid,'indexid'=>$indexid,'memberid'=>$mid])->one()->delete();
    }

    // 收藏列表页 - 带分页

    public function getListByTypeid($typeid,$page,$memberid)
    {
        $query      =       Collection::find()->alias('c')
            ->select('c.id,c.indexid,c.typeid,
            p.litpic,p.satisfyscore,p.bookcount,p.title,p.price,p.iconlist')
            ->where(['c.memberid'=>$memberid]);
        // 对 $typeid = 0 单独处理 ,即获得全部的
        if($typeid != 0 ) $query->andWhere(['c.typeid'=>$typeid]);

        switch ((int)$typeid)
        {
            case 1:
                $query->InnerJoin(Hotel::tableName() . 'as p','c.indexid=p.id' );    // 现在还未写,后面实现
                break;
            case 2:
                $query->InnerJoin(Hotel::tableName() . 'as p','c.indexid=p.id' );
                break;
            case 5:
                $query->InnerJoin(Spot::tableName() . 'as p','c.indexid=p.id');
                break;
            case 14:
                $query->InnerJoin(Tuan::tableName() . 'as p','c.indexid=p.id');
                break;
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $collectionArr = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        return $collectionArr ;

    }

    // 根据typeid 获取当前总量
    public function getCountByTypeid($typeid,$memberid)
    {
        $query      =       Collection::find()->alias('c')
            ->select('c.id,c.indexid,c.typeid,
            p.litpic,p.satisfyscore,p.bookcount,p.title,p.price,p.iconlist')
            ->where(['c.memberid'=>$memberid]);
        // 对 $typeid = 0 单独处理 ,即获得全部的
        if($typeid != 0 ) $query->andWhere(['c.typeid'=>$typeid]);

        switch ((int)$typeid)
        {
            case 1:
                $query->InnerJoin(Hotel::tableName() . 'as p','c.indexid=p.id' );    // 现在还未写,后面实现
                break;
            case 2:
                $query->InnerJoin(Hotel::tableName() . 'as p','c.indexid=p.id' );
                break;
            case 5:
                $query->InnerJoin(Spot::tableName() . 'as p','c.indexid=p.id');
                break;
            case 14:
                $query->InnerJoin(Tuan::tableName() . 'as p','c.indexid=p.id');
                break;
        }

        $totalCount = $query->count();
        return $totalCount;

    }


    // 收藏列表 - 先获得分页数据的再循环联查
    public function getListByTypeAll($memberid,$page)
    {
        $query      =       Collection::find()->alias('c')
            ->select('c.id,c.typeid,c.indexid')
            ->where(['c.memberid'=>$memberid]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $collectionArr = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        // 重组数据 - 若找不到相关的收藏,则删除收藏数据
        $collection = [];
        foreach ($collectionArr as $k=>$v)
        {
            switch ($v['typeid'])
            {
                case 1:
                    //$query->leftJoin(Hotel::tableName() . 'as p','c.indexid=p.id' );    // 现在还未写,后面实现
                    break;
                case 2:
                    $collectiobDetail = Hotel::collectionMessage($v['indexid']);;
                    if(empty($collectiobDetail))
                    {
                        self::delCollectionByids($v['id']);
                        unset($collectionArr[$k]);
                    }else{
                        $collection[$k] = $collectiobDetail;
                        $collection[$k]['typeid']   =  2;
                    }

                    break;
                case 5:
                    $collectiobDetail = Spot::collectionMessage($v['indexid']);;
                    if(empty($collectiobDetail))
                    {
                        self::delCollectionByids($v['id']);
                        unset($collectionArr[$k]);
                    }else{
                        $collection[$k] = $collectiobDetail;
                        $collection[$k]['typeid']   =  5;
                    }
                    break;

                case 14:
                    $collectiobDetail = Tuan::collectionMessage($v['indexid']);;
                    if(empty($collectiobDetail))
                    {
                        self::delCollectionByids($v['id']);
                        unset($collectionArr[$k]);
                    }else{
                        $collection[$k] = $collectiobDetail;
                        $collection[$k]['typeid']   =  14;
                    }
                    break;
            }

            $collection[$k]['id']   =  $v['id'];
        }
        return $collection ;
    }

    // 根据ids获得收藏记录
    public function getCollectionByids($ids)
    {
        return Collection::find()
            ->select('typeid,indexid,memberid')
            ->where(['id'=>$ids])
            ->asArray()
            ->all();
    }

    // 根据ids 删除收藏
    public function delCollectionByids($ids)
    {
        $collection = Collection::find()->where(['id'=>$ids])->one();
        if(!$collection)
            return false;
        else
            return $collection->delete();
    }
}