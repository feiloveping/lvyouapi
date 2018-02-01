<?php

namespace app\modules\v1\models;

use yii\db\ActiveRecord;
use yii\data\Pagination;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property string $name
 */
class Spot extends ActiveRecord
{
    public $c;
    public static function tableName()
    {
        return '{{%spot}}';
    }
    public function extraFields() {
        //return ['email'];
    }

    // 网站首页默认展示的6个景点(按照访问量排序)
    public function getSpotIndex()
    {
        $app_url = \Yii::$app->params['app_url'];
        return  Spot::find()
            ->select(["id,title,bookcount,concat('$app_url',litpic) as litpic,concat('http://lvyou.wmqt.net/phone/spots/show_',id,'.html#&pageHome') as spotdetailurl"])
            ->orderBy('shownum desc')
            ->where('ishidden=0')
            ->limit(6)
            ->asArray()
            ->all();
    }

    // 景點首頁推薦的6個熱門景點 - 默認按照銷量倒敘排列
    public function getHotSpot()
    {
        $spot = Spot::find()
            ->select('id,title,price,litpic')
            ->where(['ishidden'=>0])
            ->orderBy('bookcount desc')
            ->limit(6)
            ->asArray()
            ->all();
        return $spot;
    }

    // 無條件的景點列表帶分頁
    public function spotList($param,$page,$keyword='')
    {
        // 对参数进行解析
        $param = explode('-',$param);
        //对时间进行过滤,最大结束时间小于当前时间则不显示
        $app_url = \Yii::$app->params['app_url'];
        $query = Spot::find()
            ->select(["id,title,satisfyscore,iconlist,bookcount,price,concat('$app_url',litpic) as litpic,concat('http://lvyou.wmqt.net/phone/spots/show_',id,'.html#&pageHome') as spotdetailurl"])
            ->asArray()
            ->where('ishidden=0 and price>0');

        // 对搜索关键字
        if($keyword)
            $query->andWhere(['like','title',$keyword] );

        // 属性id
        // kindlist-综合排序-价格范围-主题
        if (count($param) != 3) return false;

        // 对城市进行筛选
        if($param[0] != 0)  $query->andwhere("find_in_set('$param[0]',kindlist)");
        // 综合筛选 - 排序
        if($param[1] != 0)
        {
            switch ($param[1])
            {
                case 1 :
                    $query->orderBy('recommendnum desc');
                    break;
                case 2:
                    $query->orderBy('price');
                    break;
                case 3:
                    $query->orderBy('price desc');
                    break;
                case 4:
                    $query->orderBy('bookcount desc');
                    break;
            }
        }
        // 对价格进行筛选 , 若第三个参数为0或者0,0 则默认为全部的意思
        // - 根據價格id找到最大最小值

        if($param[2] != '0' && $param[2] !== '0,0') {
            // 对参数处理 p,1 处理价格 4*(n%2 - 1)
            $count = count(explode(',', $param[2]));
            for ($i = 0; $i < $count / 2; $i++) {
                // 分隔字符串
                $paramThree = substr($param[2], 4 * $i, 3);
                // 判断是否为价格 - 没有分隔为数组,直接字符串取值
                if ($paramThree[0] == 'p') {
                    // 过滤全部的情况
                    if ($paramThree[2] != 0) {
                        $price = SpotPricelist::getPriceById($paramThree[2]);
                        if ($price['max'] == 'max') {
                            $query->andWhere('price>' . $price['min']);
                        } else {
                            $query->andWhere('price between ' . $price['min'] . ' and ' . $price['max']);
                        }
                    }
                } else {
                    // 处理属性参数 - 商品具体属性和商品不限属性 (第二个参数为0/第二个参数不为0)
                    if($paramThree[2] != 0 )
                        $query->andWhere("find_in_set('$paramThree[2]',attrid)");
                    else{
                        // 获取所有一级属性下的二级属性
                        $sonAttr = SpotAttr::getSonAttr($paramThree[0]);
                        foreach ($sonAttr as $sk => $sv)
                        {
                            $query->orWhere("find_in_set('$sv[id]',attrid)");
                        }
                    }

                }
            }
        }


        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $pages->page = $page -1 ;
        $spot['spot'] = $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $spot['pagecount'] = $pagecount;

        // 對列表進行重組,增加icon 圖標
        foreach ($spot['spot'] as $k=>$v)
        {
            $kindname = [];
            if($v['iconlist'])
            {
                $query = "select kind as iconname from sline_icon where find_in_set(id,'$v[iconlist]') limit 2";
                $kindname = Spot::findBysql($query)->asArray()->all();
            }
            $spot['spot'][$k]['iconlist'] = $kindname;
        }
        return $spot;
    }

    // 根据id获取具体景区数据
    public function getDetailByid($id)
    {
        $app_url = \Yii::$app->params['app_url'];
        $spotdetail = Spot::find()
            ->select('id,aid,title,shortname,price,piclist,bookcount,satisfyscore,description
            ,address,lng,lat,content,booknotice,friendtip,getway,supplierlist')
            ->where('id='.$id)
            ->asArray()->one();
        if(empty($spotdetail)) return false;
        // 处理图片列表
        $spotdetail['piclist'] = explode(',',$spotdetail['piclist']);
        foreach ($spotdetail['piclist'] as $k=>$v)
        {
            $spotdetail['piclist'][$k] = $app_url . $v ;
        }

        return $spotdetail;
    }


    // 根据景区id获得简单的信息-供订单页面使用
    public function getEasySpotByid($id)
    {
        return $spotdetail = Spot::find()
            ->select('aid,title,supplierlist,litpic')
            ->where('id='.$id)
            ->asArray()->one();
    }

    // 根据id获取收藏所需要的信息
    public function collectionMessage($id)
    {
        return Spot::find()
            ->select('id as indexid,price,litpic,bookcount,title,iconlist,satisfyscore')
            ->where('id='.$id)
            ->asArray()->one();
    }

    // 获得所有的景点
    public function getAll()
    {
        return Spot::find()
            ->select(['id','title','satisfyscore','bookcount','price','litpic','iconlist','lng','lat'])
            ->where('ishidden=0 and price>0')
            ->asArray()
            ->all();
    }



}
