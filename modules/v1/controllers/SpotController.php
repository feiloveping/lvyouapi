<?php

namespace app\modules\v1\controllers;

use app\modules\components\helpers\FeiArr;
use app\modules\components\helpers\FeiMaplocation;
use app\modules\components\helpers\GeoHash;
use app\modules\v1\models\Advertise;
use app\modules\v1\models\Collection;
use app\modules\v1\models\Comment;
use app\modules\v1\models\Destinations;
use app\modules\v1\models\Icon;
use app\modules\v1\models\Member;
use app\modules\v1\models\Piclist;
use app\modules\v1\models\Question;
use app\modules\v1\models\SearchKeyword;
use app\modules\v1\models\Spot;
use app\modules\v1\models\SpotAttr;


use app\modules\v1\models\SpotPricelist;
use app\modules\v1\models\SpotTicket;
use yii\web\Response;

class SpotController extends DefaultController
{
    public $modelClass = 'app\modules\v1\models\spot';

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


    // 景点处的banner图
    public function actionBanner()
    {
        $banner = Advertise::getSpotBanner();
        if(empty($banner)) return $banner;
        // 对查到的数据进行图片转化
        $bannerImg = unserialize($banner['adsrc']) ;
        $banner['adlink'] = unserialize($banner['adlink']);
        $banner['adname'] = unserialize($banner['adname']) ;
        $app_url = \Yii::$app->params['app_url'] ;
        foreach ($bannerImg as $k=>$v)
        {
            $bannerImg[$k] = $app_url . $v ;
        }
        $banner['adsrc'] = $bannerImg ;

        return $banner;
    }

    // 景点模块 banner和热门景点综合
    public function actionSpotIndex()
    {

        $banner     =       $this->runAction('banner');
        $spot       =       $this->runAction('spot-hot');
        $data       =       [
            'banner'=>      $banner,
            'hotspot'  =>      $spot,
        ];
        return      $data;
    }
    // 景点模块 热门景点(带图片)
    public function actionSpotHot()
    {
        $spot =  Spot::getHotSpot();
        return $spot;
    }

    // 景點板塊的列表頁
    public function actionSpotList()
    {
        $request = \Yii::$app->request;
        $keyword = strip_tags($request->get('keyword'));
        $param = $request->get('param');
        $page = $request->get('page');
        $spot = Spot::spotList($param,$page,$keyword);
        if(!$spot)
        {
            return ['code'=>200,'data'=>['spot'=>[],'pagecount'=>''],'msg'=>'未找到数据'];
        }
        return ['code'=>200,'data'=>$spot,'msg'=>'ok'];
    }

    // 对条件的综合
    public function actionCondition()
    {
        $city       =       $this->runAction('spot-city');
        $condition  =       $this->runAction('spot-other-conditions');
        $screen     =       $this->runAction('spot-screen-condition');

        $data       =       [
            'city'  =>      $city,
            'condition' =>  $condition,
            'screen'    =>  $screen,
        ];
        $data = ['code'=>200,'data'=>$data,'msg'=>'ok'];
        return  $data;
    }

    // 景点板块-列表-条件-城市
    public function actionSpotCity()
    {
        $spotcity = Destinations::getTengchongCity();
        array_unshift($spotcity,['id'=>'0','kindname'=>'全城','pinyin'=>'allcity']);
        return $spotcity ;
    }

    // 景区板块-列表-条件-综合排序
    public function actionSpotOtherConditions()
    {
        $condition = \Yii::$app->params['spot_condition_other'];
        return $condition;
    }

    // 景区板块-列表-条件-筛选条件
    public function actionSpotScreenCondition()
    {
        // 先获得价格区间
        $price = SpotPricelist::getPriceList();

        //处理价格
        foreach ($price as $k=>$v)
        {

            if($v['max'])
            {
                $price[$k] = [
                    'id'        =>  $v['id'],
                    'attrname'  =>  '¥'.$v['min'] . '-' . '¥'.$v['max'],
                    'pid'       =>  'p',
                ];
            }else{
                $price[$k] = [
                    'id'        =>  (string) $v['id'],
                    'attrname'  =>  '¥'.$v['min'] . '以上',
                    'pid'       =>  'p',
                ];
            }

            if($v['max'] == '全部')
            {
                $price[$k] = [
                    'id'        =>  $v['id'],
                    'attrname'  =>  '全部',
                    'pid'       =>  'p',
                ];
            }
        }

        $condition[] = ['id'=>'p','attrname'=>'价格范围','pid'=>'','son'=>$price];
        // 获得景区筛选条件
        $attr = SpotAttr::getAttrAll();

        // 对每个景区的属性增加全部
        foreach ($attr as $k=>$v)
        {
            array_unshift($attr[$k]['son'],['id'=>'0','attrname'=>'全部','pid'=>$v['id']]);
            $condition[] = $v;
        }

        // 增加全部全部
        array_unshift($condition,['id'=>'0','attrname'=>'全部','pid'=>'0','son'=>[['id'=>'0','attrname'=>'全部','pid'=>'0']]]);
        return      $condition;
    }

    // 景区详情页
    public function actionSpotDetail()
    {
        $id = (int) \Yii::$app->request->get('id','0');
        if( ! $id) return array('code'=>-1,'data'=>'','msg'=>'参数错误');
        $spotdetail = Spot::getDetailByid($id);
        if(!$spotdetail)
            return ['code'=>404,'data'=>'','msg'=>'未找到数据'];

        // 根据景点id计算景区的评价
        $typeid     =       \Yii::$app->params['typeid']['spot'];
        $spotdetail['comment'] = Comment::getCommentCountByTypeId($typeid , $id);
        // 根据景点id查询相关门票
        $ticketRe = SpotTicket::getTicketBySpotId($id);
        // 对门票数据进行处理,分级说明以及价格
        foreach ($ticketRe as $k=>$v)
        {
            $kindname       =       $v[0]['kindname'];
            foreach ($v as $k2=>$v2)
            {
                $lastoffer          =       unserialize($v2['lastoffer']);
                unset($v[$k2]['lastoffer']);
                $v[$k2][ 'sellprice']     =      $lastoffer['adultprice'];
                $v[$k2][ 'marketPrice']   =      $lastoffer['adultmarketprice'];
            }
            $ticket[]       =          [
                'kindid'    =>      $k,
                'kindname'  =>      $kindname,
                'ticket'    =>      $v
            ];
        }
        $spotdetail['ticket'] = $ticket ;
        // 增加对是否收藏的判断
        $spotdetail['iscollection'] = 0;
        if($this->logsign) {
            if(Collection::isCollection($typeid,$id,$this->mid)) $spotdetail['iscollection'] = 1;
        }
        return ['code'=>200,'data'=>$spotdetail,'msg'=>'ok'];
    }

    // 景区详情页 - 门票的购买须知
    public function actionTicketNotes()
    {
        $id = \Yii::$app->request->get('id');
        $ticketnotes = SpotTicket::getDesByTicketId($id);
        if(empty($ticketnotes))
            return ['code'=>404,'data'=>'','msg'=>'未找到数据'];
        else
            return ['code'=>200,'data'=>$ticketnotes,'msg'=>'ok'];
    }

    // 景点收藏
    public function actionSpotAddCollection()
    {
        if(! $this->logsign) return array('code'=>401,'data'=>'','msg'=>'用户未登录');
        $typeid = 5;
        $mid = $this->mid;
        $indexid = \Yii::$app->request->get('id');
        $re =  \Yii::$app->runAction('v1/collection/add-collection',['typeid'=>$typeid,'indexid'=>$indexid,'mid'=>$mid]);
        if($re)
            return array('code'=>200,'data'=>$re,'msg'=>'ok');
        else
            return array('code'=>400,'data'=>'','msg'=>'收藏失败');
    }

    // 景点收藏取消
    public function actionSpotDelCollection()
    {
        if(! $this->logsign) return array('code'=>401,'data'=>'','msg'=>'用户未登录');
        $typeid = 5;
        $mid = $this->mid;
        $indexid = \Yii::$app->request->get('id');
        $re =  \Yii::$app->runAction('v1/collection/del-collection',['typeid'=>$typeid,'indexid'=>$indexid,'mid'=>$mid]);
        if($re)
            return array('code'=>200,'data'=>$re,'msg'=>'ok');
        else
            return array('code'=>400,'data'=>$re,'msg'=>'删除收藏失败');
    }

    // 景点图集列表
    public function actionSpotPicList()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',0);
        $typeid = \Yii::$app->params['typeid']['spot'];

        $pic = Piclist::getPicListById($typeid,$id);
        if(empty($pic))
            return ['code'=>404,'data'=>'','msg'=>'信息未找到'];
        else
            return ['code'=>200,'data'=>$pic,'msg'=>'ok'];
    }

    // 景点问答列表
    public function actionSpotQuestion()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',-1);
        $typeid =   \Yii::$app->params['typeid']['spot'];
        $where = [
            'typeid'    =>  $typeid,
            'productid' =>  $id
        ];
        $question = Question::getQuestionByTypeId($where);
        if(empty($question))
            return ['code'=>404 , 'msg'=>'未找到数据','data'=>''];
        else
            return ['code'=>200 , 'msg'=>'ok','data'=>$question] ;

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

    // 景区问答发布
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

    // 附近的景点
    public function actionSpotNear()
    {
        $spot = $this->runAction('all-spot');
        $request = \Yii::$app->request;
        $lng = $request->get('lng',98.50);
        $lat = $request->get('lat',25.03);
        $page = $request->get('page',1);
        // 对拿到的数据计算距离并排序
        $map = new FeiMaplocation();
        foreach ($spot as $k=>$v)
        {
            $metre = $map->getdistances((double)$lat,(double)$lng,(double)$v['lat'],(double)$v['lng']);
            $spot[$k]['metre'] = $map->getMetre($metre);
        }
        $feiArr = new FeiArr();
        $spot = $feiArr->my_sort($spot,'metre');
        $spot = $feiArr->pageArr($spot,$page);
        // 对总页数进行处理
        if(empty($spot) || $page>$spot['pagecount'])
            return ['code'=>200,'data'=>'','msg'=>'未找到数据'];
        else
            return ['code'=>200,'data'=>$spot,'msg'=>''];
    }

    // 获得所有的数据并缓存
    public function actionAllSpot()
    {
        $spotModel        =       new Spot();
        $cache            =       \Yii::$app->cache;
        $key              =       'all-spot';
        $spot             =       $cache->getOrSet($key,function() use($spotModel) {
            $spot = $spotModel->getAll();
            $app_url = \Yii::$app->params['app_url'];
            foreach ($spot as $k=>$v)
            {
                $iconlists = explode(',',$v['iconlist']);
                $spot[$k]['iconlist'] = Icon::getIconTwoNameByIds($iconlists);
                if(!empty($v['litpic']) && !strpos($v['litpic'],'ttp'))
                    $spot[$k]['litpic'] = $app_url . $v['litpic'];
            }
            return $spot;
        },60);

        return $spot;
    }

}
