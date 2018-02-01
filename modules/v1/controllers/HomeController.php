<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/2
 * Time: 14:05
 */

namespace app\modules\v1\controllers;


use app\modules\components\helpers\FeiMaplocation;
use app\modules\components\helpers\MyDateFormat;
use app\modules\v1\models\Advertise;
use app\modules\v1\models\Article;
use app\modules\v1\models\Hotel;
use app\modules\v1\models\HotelAttr;
use app\modules\v1\models\Line;
use app\modules\v1\models\Notes;
use app\modules\v1\models\Photo;
use app\modules\v1\models\Question;
use app\modules\v1\models\Spot;
use app\modules\v1\models\Toilet;
use app\modules\v1\models\Tuan;

class HomeController extends DefaultController
{
    public $modelClass = '';

    // 首页banner图  - 有问题,目前是写死的
    public function actionGetBannerIndex()
    {
        //id,adsrc,adname,adlink
        $banner = Advertise::getIndexBanner();
        $banner['adsrc'] = unserialize($banner['adsrc']);
        $app_url = \Yii::$app->params['app_url'];
        foreach ($banner['adsrc']  as $k=>$v)
        {
            $banner['adsrc'][$k] = $app_url . $v;
        }
        $banner['adname'] = unserialize($banner['adname']);
        $banner['adlink'] = unserialize($banner['adlink']);

        return $banner ;


    }

    // 網站首頁默認展示的6個景點 - 展示量排序
    public function actionGetSpotIndex()
    {
        $spot = Spot::getSpotIndex();
        $app_url = \Yii::$app->params['app_url'];
        foreach ($spot as $k=>$v)
        {
            if($v['litpic'])
                $spot[$k]['litpic'] = $app_url . $v['litpic'];
        }
        return $spot ;

    }

    // 網站首頁默認展示的6個酒店屬性 -  直接排序6個
    public function actionGetHotelIndex()
    {
        $hotel = HotelAttr::getHotelIndex();
        $app_url = \Yii::$app->params['app_url'];
        foreach ($hotel as $k=>$v)
        {
            if($v['litpic'])
                $hotel[$k]['litpic'] = $app_url . $v['litpic'];
        }
        return $hotel ;

    }

    // 网站首页默认展示的6个热门住宿
    public function actionGetHotelIndex6()
    {
        $hotel = Hotel::hotelIndex6();
        $app_url = \Yii::$app->params['app_url'];
        if(!empty($hotel))
            foreach ($hotel as $k=>$v)
            {
                $hotel[$k]['litpic'] = $app_url . $v['litpic'];
                $hotel[$k]['title'] = mb_substr($v['title'],0,7);
            }

        return $hotel ;


    }

    // 网站首页热门攻略 - 上方
    public function actionGetArticleUpIndex()
    {
        $article = Article::getIndexArticleUp();
        return $article ;
    }

    // 网站首页热门攻略
    public function actionGetArticleIndex()
    {
        $article = Article::getIndexArticle();
        return $article ;
    }

    // 网站首页热门团购
    public function actionGetTuanIndex()
    {
        $tuan = Tuan::getIndexTuan();
        if(! empty($tuan)){
            // 统计时长
            foreach ($tuan as $k=>$v)
            {
                $tuan[$k]['endtime'] = MyDateFormat::getAfterDayHourmin($v['endtime']);
            }
        }
        return $tuan;
    }

    // 网站首页热门游记
    public function actionGetNotesIndex()
    {
        $notes = Notes::getIndexNotes();

        return $notes;

    }

    // 网站首页热门相册
    public function actionGetPhotoIndex()
    {
        $photo = Photo::getIndexPhoto();

        return $photo;
    }

    // 首页线路
    public function actionGetLineIndex()
    {
        $line =  Line::getListerHome();
        $app_url = \Yii::$app->params['app_url'];
        foreach ($line as $k=>$v)
        {
            $line[$k]['litpic'] = $app_url . $v['litpic'];
        }
        return $line;
    }

    // 网站首页热门问答
    public function actionGetQuestionIndex()
    {
        $question = Question::getIndexQuestion();
        if(!empty($question))
        {
            foreach ($question as $k=>$v)
            {
                $question[$k]['replycontent'] = strip_tags($v['replycontent']);
            }
        }

        return $question;

    }

    // 首页信息综合
    public function actionHome()
    {
        $banner     =   $this->runAction('get-banner-index');
        $spot       =   $this->runAction('get-spot-index');
        $hotel      =   $this->runAction('get-hotel-index6');
        $article    =   $this->runAction('get-article-index');
        $articleup  =   $this->runAction('get-article-up-index');
        $tuan       =   $this->runAction('get-tuan-index');
        $notes      =   $this->runAction('get-notes-index');
        $photo      =   $this->runAction('get-photo-index');
        $question   =   $this->runAction('get-question-index');
        $line       =   $this->runAction('get-line-index');

        $data = [
            'banner'        =>      $banner,
            'spot'          =>      $spot,
            'hotel'         =>      $hotel,
            'articleup'     =>      $articleup,
            'article'       =>      $article,
            'tuan'          =>      $tuan,
            'notes'         =>      $notes,
            'photo'         =>      $photo,
            'question'      =>      $question,
            'line'          =>      $line,
        ] ;

        return $data;
    }


}