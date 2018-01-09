<?php

namespace app\modules\v1\models;

use yii\data\Pagination;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property string $name
 */
class Hotel extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotel}}';
    }

    // 根據屬性id獲得預定量
    public function getHotelBookCountByAttrid($aid)
    {
        return Hotel::find()
            ->select('sum(bookcount) as bookcount')
            ->where("find_in_set('$aid',attrid) and ishidden=0")
            ->asArray()->one();
    }

    // 酒店列表頁    0城市 - 1價格星級 -2綜合排序 - 3屬性篩選
    public function hotelList($param,$page,$keyword = '',$time = '')
    {
        $params = explode('-',$param);
        // 对属性进行验证
        if(count($params) != 4)
        {
            return false;
        }
        $app_url = \Yii::$app->params['app_url'];
        $query = Hotel::find()->alias('h')
                    ->select('h.id,h.title,h.seotitle,h.iconlist,concat(\''. $app_url .'\',`h`.`litpic`) as litpic,h.price,h.satisfyscore,h.bookcount')
                    ->where('h.ishidden=0');
        // 对搜索处理
        if($keyword)  $query->andWhere('h.title like \'% ' . $keyword . ' \'%');
        // 对时间进行处理
        if ($time)
        {
            $hoteltime = explode('-',$time);
            if($hoteltime[0] && $hoteltime[1] == '') $query->innerJoin('(select distinct hotelid from  sline_hotel_room_price where day>' . $hoteltime[0] . ' group by suitid having count(*)>1 ) as hr','h.id=hr.hotelid');
            if($hoteltime[0] == '' && $hoteltime[1]) $query->innerJoin('(select distinct hotelid from  sline_hotel_room_price where day<' . $hoteltime[1] . ' group by suitid having count(*)>1 ) as hr','h.id=hr.hotelid');
            if($hoteltime[0] < $hoteltime[1]) $query->innerJoin('(select distinct hotelid from  sline_hotel_room_price where day between ' . $hoteltime[0] . ' and ' . $hoteltime[1] . ' group by suitid having count(hotelid)>1 ) as hr','h.id=hr.hotelid');

        }
        // 参数 1 城市id
        if($params[0] != 0) $query->andWhere("find_in_set($params[0] , h.kindlist)");
        // 参数 2 价格星级 - 价格
        $pricestar = explode(',',$params[1]);
        $priceid      =   $pricestar[0];
        $starid       =   $pricestar[1];
        if($priceid != 0)
        {
            // 判断是否为最大id
            $maxPrice = HotelPricelist::getPriceMaxId();
            if($priceid > $maxPrice['id'])
            {
                $query->andWhere('h.price>'.$maxPrice['max']);
            }else{
                $price = HotelPricelist::getPriceListById($priceid);
                $query->andWhere('h.price between '.$price['min'] . ' and  '.$price['max']) ;
            }
        }
        // 参数 2 价格星级 - 星级
        if($starid != 0)
            $query->andWhere('h.hotelrankid='.$starid);


        // 参数 3 综合
        switch ($params[2]){
            case 0:
                $query->orderBy('h.satisfyscore desc');
                break;
            case 1:
                $query->orderBy('h.shownum desc');
                break;
            case 2:
                $query->orderBy('h.price asc');
                break;
            case 3:
                $query->orderBy('h.price desc');
                break;
            case 4:
                $query->orderBy('h.bookcount desc');
        }
        // 参数 4 属性 一级.二级
        $params4 = explode(',' ,$params[3]) ;
        foreach ($params4 as $k=>$v)
        {
            if($v != 0) $query->andWhere('find_in_set(\'' .$v. '\',h.attrid)');
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = \Yii::$app->params['page_size'];
        $pagecount = $pages->getPageCount();
        if($page > $pagecount)
            return false;
        $hotelArr = $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        $hotel['pagecount'] = $pagecount;

        // 对酒店的标签进行连表查询
        foreach ($hotelArr as $k=>$v)
        {
            $iconids = explode(',',$v['iconlist']);
            foreach ($iconids as $v2)
            {
                unset($hotelArr[$k]['iconlist']);
                $hotelArr["$k"]['iconlist'][] = Icon::getIconNameByIds($v2);
            }
        }
        $hotel['hotel'] = $hotelArr ;

        return $hotel;


    }

    // 酒店详情页
    public function hotelDetail($id)
    {
        $hotel      =       Hotel::find()
            ->alias('h')
            ->select('h.title,h.sellpoint,h.telephone,h.content,h.address,h.price,
            h.shownum,h.keyword,h.description,h.litpic,h.piclist,h.opentime,h.decoratetime,
            h.lng,h.lat,h.fuwu,
            h.satisfyscore,h.bookcount,ht.hotelrank,hef.e_content_4 as zhengce')
            ->where('h.id=' . $id . ' and h.ishidden=0')
            ->leftJoin(HotelRank::tableName() . ' as ht','h.hotelrankid=ht.id')
            ->leftJoin(HotelExtendField::tableName() . ' as hef','hef.productid=h.id')
            ->asArray()
            ->one();
        return $hotel;
    }


    // 网站首页默认展示的6个热门住宿
    public function hotelIndex6()
    {
        $app_url = \Yii::$app->params['app_url'];
        return Hotel::find()
            ->select('id,bookcount,title,price,concat(\'' . $app_url . '\',`litpic` ) as litpic')
            ->where('ishidden=0')
            ->orderBy('bookcount desc')
            ->limit('6')
            ->asArray()->all();
    }

    //根据酒店id选择简单的几个数据
    public function hotelEasyDetail($id)
    {
        $hotel      =       Hotel::find()
            ->select('aid,supplierlist,title')
            ->where('id=' . $id )
            ->asArray()
            ->one();
        return $hotel;
    }

    // 根据id获取收藏所需要的信息
    public function collectionMessage($id)
    {
        return Hotel::find()
            ->select('id as indexid,price,litpic,bookcount,title,iconlist,satisfyscore')
            ->where('id='.$id)
            ->asArray()->one();
    }

}
