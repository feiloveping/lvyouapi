<?php

namespace app\modules\v1\controllers;

use app\modules\components\helpers\FeiArr;
use app\modules\components\helpers\FeiMaplocation;
use app\modules\v1\models\Advertise;
use app\modules\v1\models\Collection;
use app\modules\v1\models\Comment;
use app\modules\v1\models\Destinations;
use app\modules\v1\models\Hotel;
use app\modules\v1\models\HotelAttr;
use app\modules\v1\models\HotelPricelist;
use app\modules\v1\models\HotelRank;
use app\modules\v1\models\HotelRoom;
use app\modules\v1\models\Icon;
use app\modules\v1\models\Member;
use app\modules\v1\models\Piclist;
use app\modules\v1\models\Question;
use yii\web\Response;



class HotelController extends DefaultController
{
    public $modelClass = 'app\modules\v1\models\Hotel';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actions() {
        $actions = parent::actions();
        // 禁用""index,delete" 和 "create" 操作
        unset($actions['index'],$actions['delete'], $actions['create']);
        return $actions;
    }

    // 酒店列表頁
    public function actionHotelList()
    {
        $request = \Yii::$app->request;
        $param = $request->get('param');
        $page = $request->get('page');
        $keyword = strip_tags($request->get('keyword'));
        $time = $request->get('time');
        $hotel = Hotel::hotelList($param,$page,$keyword,$time);
        if(!$hotel) return ['code'=>404,'data'=>'','msg'=>'未找到数据'];
        return ['code'=>200,'data'=>$hotel,'msg'=>'ok'];
    }

    // 酒店条件-综合
    public function actionHotelCondition()
    {
        $city       =       $this->runAction('hotel-city');
        $pricestar  =       $this->runAction('hotel-price-star');
        $othercondition =   $this->runAction('hotel-other-conditions');
        $screencondition=   $this->runAction('hotel-screen-condition');
        $data       =       [
            'city'      =>  $city,
            'pricestar' =>  $pricestar,
            'othercondition'    =>  $othercondition,
            'screencondition'   =>  $screencondition
        ];

        return ['code'=>200,'data'=>$data,'msg'=>'ok'];
    }

    // 酒店板块-列表-条件-城市
    public function actionHotelCity()
    {
        $spotcity = Destinations::getTengchongCity();
        array_unshift($spotcity,['id'=>404,'kindname'=>'全城','pinyin'=>'allcity']);
        return $spotcity;
    }

    // 酒店板塊-列表-條件-價格星級
    public function actionHotelPriceStar()
    {
        $price = HotelPricelist::getPriceList();
        array_unshift($price,['id'=>0,'min'=>null,'max'=> null]);
        $star = HotelRank::getRank();
        array_unshift($star,['id'=>0,'hotelrank'=>'不限']);
        return ['price'=>$price,'star'=>$star];
    }

    // 酒店模塊-列表-條件-綜合
    public function actionHotelOtherConditions()
    {
        $condition = \Yii::$app->params['spot_condition_other'];
        return $condition;
    }

    // 酒店模塊-列表-條件-篩選
    public function actionHotelScreenCondition()
    {
        // 對一級屬性和二級屬性進行篩選
        $screen =  HotelAttr::getHotelAttr();
        // 分別對二級屬性添加不限
        foreach ($screen as $k=>$v)
        {
            if(empty($screen[$k]['son'])) $screen[$k]['son'] = [] ;
            array_unshift($screen[$k]['son'],['id'=>0,'pid'=>$v['id'],'attrname'=>'不限']);
        }
        return $screen;
    }

    //  酒店详情页
    public function actionHotelDetail()
    {

        $request    =   \Yii::$app->request;
        $id         =   $request->get('id');
        $hotel      =   Hotel::hotelDetail($id) ;
        if(empty($hotel)) return ['code'=>400,'msg'=>'信息未找到','data'=>''];

        // 对图片进行处理
        $app_url    =   \Yii::$app->params['app_url'] ;
        if(!empty($hotel))
        {
            $hotel['litpic']    =   $app_url    .   $hotel['litpic'];
            $hotel['piclist']   =   explode(',',$hotel['piclist']);
            foreach ($hotel['piclist'] as $k=>$v)
            {
               $hotel['piclist'][$k]   =   $app_url    .   $v;
            }
        }

        // 统计评价
        $hotel['commentnum'] = Comment::getCommentCountByTypeId(2 , $id);

        // 对酒店的房间做统计
        $room      =  HotelRoom::getRoomById($id) ;
        $hotel['room']  = $room;

        // 酒店的房间类型处理
        foreach ($room as $k1=>$v1)
        {
            $piclist = explode(',',$room[$k1]['piclist']);
            unset($hotel['room'][$k1]['piclist']);
            foreach ($piclist as $k2=>$v2)
            {
                $hotel['room'][$k1]['piclist'][$k2] = $app_url . $v2 ;
            }
        }
        // 增加对是否收藏的判断
        $hotel['iscollection'] = 0;
        if($this->logsign) {
            $typeid = \Yii::$app->params['typeid']['hotel'];
            if(Collection::isCollection($typeid,$id,$this->mid)) $hotel['iscollection'] = 1;
        }
        return      ['code'=>200,'data'=>$hotel,'msg'=>'ok'];
    }

    // 根据房型id获取房型的具体信息
    public function actionRoomDetail()
    {
        $request        =       \Yii::$app->request;
        $roomid         =       (int) $request->get('id',0);
        $roomdetail = HotelRoom::getRoomDetailById($roomid);
        if(empty($roomdetail))
            return ['code'=>404 , 'msg'=>'未找到数据','data'=>''];
        else
            return ['code'=>200 , 'msg'=>'ok','data'=>$roomdetail] ;

    }


    public function actionHotelAddCollection()
    {
        if(!$this->logsign)
            return  ['code'=>401,'msg'=>'用户未登录','data'=>''];
        $id         =       \Yii::$app->request->get('id',0);
        $typeid     =       2;
        $mid        =       $this->mid;
        $re = \Yii::$app->runAction('v1/collection/add-collection',array('typeid'=>$typeid,'indexid'=>$id,'mid'=>$mid));
        if(!$re)
            return ['code'=>400,'data'=>'','msg'=>'收藏失败'] ;
        else
            return ['code'=>200,'data'=>'','msg'=>'收藏成功'] ;
    }

    public function actionHotelDelCollection()
    {
        if(!$this->logsign)
            return  ['code'=>401,'msg'=>'用户未登录','data'=>''];
        $typeid = \Yii::$app->params['typeid']['hotel'];
        $id     =   \Yii::$app->request->get('id');
        $mid    =   $this->mid;
        $re = \Yii::$app->runAction('v1/collection/del-collection',array('typeid'=>$typeid,'indexid'=>$id,'mid'=>$mid));
        if($re) return ['code'=>200,'msg'=>'ok','data'=>''];
        else  return ['code'=>400,'msg'=>'用户还未收藏','data'=>''];
    }

    // 景点主搜索bannner图
    public function actionBanner()
    {
        $app_url            = \Yii::$app->params['app_url'];
        $banner             = Advertise::getHotelBanner();

        if(empty($banner)) return  ['code'=>404,'msg'=>'信息未找到','data'=>null];
        $banner['adsrc']    = unserialize($banner['adsrc']);
        $banner['adlink']    = unserialize($banner['adlink']);
        $banner['adname']    = unserialize($banner['adname']);
        foreach ($banner['adsrc'] as $k=>$v)
        {
            $banner['adsrc'][$k] = $app_url . $v;
        }
        return ['code'=>200,'msg'=>'ok','data'=>$banner];
    }

    // 景点图集列表
    public function actionHotelPicList()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',0);
        $typeid = \Yii::$app->params['typeid']['hotel'];

        $pic = Piclist::getPicListById($typeid,$id);
        if(empty($pic))
            return ['code'=>404,'data'=>'','msg'=>'信息未找到'];
        else
            return ['code'=>200,'data'=>$pic,'msg'=>'ok'];
    }

    // 点评列表
    public function actionCommentList()
    {
        $request                =       \Yii::$app->request;
        $id                     =       $request->get('id');
        $page                   =       $request->get('page');
        $typeid                 =       \Yii::$app->params['typeid']['hotel'];
        $commentPage            =       Comment::getCommentByPage($typeid,$id,$page);
        if(! $commentPage) return ['code'=>404,'msg'=>'未找到数据','data'=>''];
        $commentListerArr       =       $commentPage['commentlist'] ;
        $commentPageCount       =       $commentPage['pagecount'] ;
        if(empty($commentListerArr)) return ['code'=>404,'msg'=>'未找到数据','data'=>''];

        $app_url                =       \Yii::$app->params['app_url'];
        // 对拿到的信息进行虚拟用户和真是用户信息混合
        foreach ($commentListerArr as $k=>$v)
        {
            if(!$v['vr_nickname'])
            {
                $commentLister[$k] = [
                  'content'     =>      $v['content'],
                    'addtime'   =>      $v['addtime'],
                    'piclist'   =>      $v['piclist'],
                    'star'      =>      $v['star'],
                    'nickname'  =>      $v['nickname'],
                    'rank'      =>      $v['rank'],

                ];
            }else{
                $commentLister[$k] = [
                    'content'     =>      $v['content'],
                    'addtime'   =>      $v['addtime'],
                    'star'      =>      $v['star'],
                    'nickname'  =>      $v['vr_nickname'],
                    'rank'      =>      $v['vr_grade'],
                ];
            }
            $piclist            =       explode(',',$v['piclist']);
            if (!empty($piclist))
            {
                foreach ($piclist as $k1=>$v1)
                {
                    if(empty($v1)) break;
                    $piclist[$k1]    =   $app_url    .   $v1;
                }
            }

            $commentLister[$k]['piclist']   =   $piclist;

        }
        $data       =       ['comment'=>$commentLister,'pagecount'=>$commentPageCount];
        return ['code'=>200,'data'=>$data,'msg'=>'ok'];
    }

    // 点评上部信息总计
    public function actionCommentCount()
    {
        $commentObj             =       new Comment();
        $request                =       \Yii::$app->request;
        $typeid                 =       \Yii::$app->params['typeid']['hotel']   ;
        $hotelid                =       $request->get('id');
        $commentArr             =       $commentObj->getCommentStarCount($typeid,$hotelid);
        $commentCount           =       $commentObj->getLevelComment($commentArr);
        $commentCount['count']  =       $commentObj->getCommentCountByTypeId($typeid,$hotelid);
        $commentCount['imgcount']=      $commentObj->getCommentHasImg($typeid,$hotelid);
        return ['code'=>200,'data'=> $commentCount,'msg'=>'ok'];
    }

    // 酒店的问答
    public function actionHotelQuestion()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',-1);
        $typeid =   \Yii::$app->params['typeid']['hotel'];
        $where = [
            'typeid'    =>  $typeid,
            'productid' =>  $id,
        ];
        $question = Question::getQuestionByTypeId($where);
        if(empty($question))
            return ['code'=>404 , 'msg'=>'未找到数据','data'=>''];
        else
            return ['code'=>200 , 'msg'=>'ok','data'=>$question] ;

    }

    // 酒店问答发布
    public function actionQuestionAdd()
    {
        if(!$this->logsign)
            return ['code'=>401,'msg'=>'用户未登录','data'=>''];
        $reqest = \Yii::$app->request;
        $content = htmlentities(strip_tags($reqest->post('content',0)));
        $id = $reqest->post('id');
        $ip = $reqest->getUserIP();
        if(mb_strlen($content) < 5 ) return ['code'=>404,'msg'=>'请至少输入5个字','data'=>''];
        $isanonymous = $reqest->post('isanonymous',0);      // 1 匿名    0 不匿名
        $member = $this->actionGetNickname() ;
        $mobile = $member['mobile'] ;
        $nickname = $member['nickname'] ;
        if($isanonymous == 1)
            $nickname = '匿名';

        $data = [
            'typeid'        =>      5,
            'productid'     =>      $id,
            'content'       =>      $content,
            'nickname'      =>      $nickname,
            'ip'            =>      $ip,
            'memberid'      =>      $this->mid,
            'addtime'       =>      time(),
            'phone'         =>      $mobile,
        ] ;

        // 新增酒店的问答
        $re = Question::insertQuestion($data);
        if($re != false)
            return ['code'=>200 , 'msg'=>'ok' ,'data'=>''];
        else
            return ['code'=>403 , 'msg'=>'新增失败' ,'data'=>''];
    }

    // 是否登录-若登录获取昵称
    public function actionGetNickname()
    {
        if($this->logsign){
            // 根据用户id拿到用户昵称
            return Member::getMember(['mid'=>$this->mid]);

        }else
            return false ;
    }

    // 附近的酒店
    public function actionHotelNear()
    {
        $hotel = $this->runAction('all-hotel');
        $request = \Yii::$app->request;
        $lng = $request->get('lng',98.50);
        $lat = $request->get('lat',25.03);
        $page = $request->get('page',1);
        // 对拿到的数据计算距离并排序
        $map = new FeiMaplocation();
        foreach ($hotel as $k=>$v)
        {
            $metre = $map->getdistances((double)$lat,(double)$lng,(double)$v['lat'],(double)$v['lng']);
            $hotel[$k]['metre'] = $map->getMetre($metre);
        }
        $feiArr = new FeiArr();
        $hotel = $feiArr->my_sort($hotel,'metre');
        $hotel = $feiArr->pageArr($hotel,$page);
        // 对总页数进行处理
        if(empty($hotel) || $page>$hotel['pagecount'])
            return ['code'=>200,'data'=>['pagecount'=>'','data'=>[]],'msg'=>'未找到数据'];
        else
            return ['code'=>200,'data'=>$hotel,'msg'=>''];
    }

    // 获得所有的数据并缓存
    public function actionAllHotel()
    {
        $hotelModel        =       new Hotel();
        $cache            =       \Yii::$app->cache;
        $key              =       'all-hotel';
        $hotel             =       $cache->getOrSet($key,function() use($hotelModel) {
            $hotel = $hotelModel->getAll();
            $app_url = \Yii::$app->params['app_url'];
            foreach ($hotel as $k=>$v)
            {
                $iconlists = explode(',',$v['iconlist']);
                $hotel[$k]['iconlist'] = Icon::getIconTwoNameByIds($iconlists);
                if(!empty($v['litpic']) && !strpos($v['litpic'],'ttp'))
                    $hotel[$k]['litpic'] = $app_url . $v['litpic'];
            }
            return $hotel;
        },60);

        return $hotel;
    }
}
