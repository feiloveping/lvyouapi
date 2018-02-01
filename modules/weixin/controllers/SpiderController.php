<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/25
 * Time: 15:01
 */

namespace app\modules\weixin\controllers;


use function foo\func;
use QL\QueryList;
use yii\web\Controller;

class SpiderController extends Controller
{
    // 测试1 , 获取html页面的信息
    public function actionTest1()
    {


        $html = <<<STR
<div id="one">
    <div class="two">
        <a href="http://querylist.cc">QueryList官网</a>
        <img src="http://querylist.com/1.jpg" alt="这是图片">
        <img src="http://querylist.com/2.jpg" alt="这是图片2">
    </div>
    <span>其它的<b>一些</b>文本</span>
</div>        
STR;
        $rules = array(
            //采集id为one这个元素里面的纯文本内容
            'text' => array('#one','text'),
            //采集class为two下面的超链接的链接
            'link' => array('.two>a','href'),
            //采集class为two下面的第二张图片的链接
            'img' => array('.two>img','src'),
            //采集span标签中的HTML内容
            'other' => array('span','html')
        );
        $data = QueryList::Query($html,$rules)->data;

        echo '<pre>';
        print_r($data);

    }

    // 测试2 ,记录日志
    public function actionSetLog()
    {
        QueryList::setLog('./log/ql.log');
//获取采集对象
        $hj = QueryList::Query('http://www.baidu.com/s?wd=QueryList',array(
            'title'=>array('h3','text'),
            'link'=>array('h3>a','href')
        ));
//输出结果：二维关联数组
        print_r($hj->data);
    }

    // 测试3
    public function actionReset()
    {
        QueryList::setLog('./log/ql.log');
        $html =<<<STR
<div class="xx">
    <span>
        xxxxxxxx
    </span>
    <img src="/path/to/1.jpg" alt="">
</div>
STR;
//采集文本
        $ql = QueryList::Query($html,array(
            'txt' => array('span:eq(0)','text')
        ));
        print_r($ql->data);
//采集图片
        $ql->setQuery(array(
            'image' => array('.xx img','src')
        ));
        print_r($ql->data);
    }

    // 测试4 - 获取数据再处理
    public function actionGetData()
    {
        $html =<<<STR
    <div class="xx">
        <img data-src="/path/to/1.jpg" alt="">
    </div>
    <div class="xx">
        <img data-src="/path/to/2.jpg" alt="">
    </div>
    <div class="xx">
        <img data-src="/path/to/3.jpg" alt="">
    </div>
STR;
        $baseUrl = 'http://xxxx.com';
        $data = QueryList::Query($html,array(
            'image' => array('.xx>img','data-src')
        ))->getData(function ($item) use($baseUrl){
            return $baseUrl . $item['image'];
        });

        var_dump($data);
    }

    // 测试5 - 混合现实 失败
    public function actionRealGo()
    {
        $url = 'http://36kr.com/search/articles/%E6%B7%B7%E5%90%88%E7%8E%B0%E5%AE%9E?page=1&ts=1516867622753';
        $roles = [
            'all'=>['ul>li','html'],
        ];
        $re = QueryList::Query($url,$roles)->data;
        var_dump($re);
    }

    // 测试6 - 混合现实 接口
    public function actionApiTest(){

        // 列表 http://36kr.com/api//search/entity-search?page=1&per_page=50&keyword=%E6%B7%B7%E5%90%88%E7%8E%B0%E5%AE%9E&entity_type=post&_=1516869965235
        // 详情 http://36kr.com/api/post/5110511/next?_=1516870762440
    }

    // 测试7 - 文件锁
    /**
     * 要取得共享锁定（读取的程序），将 lock 设为 LOCK_SH（PHP 4.0.1 以前的版本设置为 1）。
       要取得独占锁定（写入的程序），将 lock 设为 LOCK_EX（PHP 4.0.1 以前的版本中设置为 2）。
       要释放锁定（无论共享或独占），将 lock 设为 LOCK_UN（PHP 4.0.1 以前的版本中设置为 3）。
       如果不希望 flock() 在锁定时堵塞，则给 lock 加上 LOCK_NB（PHP 4.0.1 以前的版本中设置为 4）。
     */
    public function actionLock()
    {
        $file = './log/ql.log';
        $words = 'hello 111';
        $f = fopen($file,'r+');

        var_dump(file_get_contents($file));
        $re = flock($f,LOCK_EX);
        var_dump($re);
        if(flock($f,LOCK_EX))
        {
            $re = fwrite($f,'ok');
            echo '111';
        }else{
            echo 'sorry';
        }
        var_dump(file_get_contents($file));
        fclose($f);
    }

    public function actionLock2()
    {

        $fileName = './log/ql.log';
        $dataToSave = '1234123421';
        if($fp=fopen($fileName,'a')){
            $startTime = microtime();
            do{
                $canWrite=flock($fp,LOCK_EX);
                if(!$canWrite){
                    usleep(round(rand(0,100)*1000));
                }
            }while((!$canWrite)&&((microtime()-$startTime)<1000));
            if($canWrite){
                fwrite($fp,$dataToSave);
            }
            fclose($fp);
        }
        echo $this->actionGetTime();

    }

    public function actionGetTime()
    {
        $re = explode(' ',microtime());
        $re =$re[1] . substr($re[0],2,-1);
        return $re;
    }

}