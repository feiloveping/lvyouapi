<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/24
 * Time: 16:41
 */
use app\assets\WeixinToilet;
WeixinToilet::register($this);
?>
<?php $this->beginPage(); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <title>智慧腾冲</title>
        <style>
            .bbc_img:after{height:1px;background:linear-gradient(to left,#fff,#e5e5e5,#fff);display: block;content:"";}
            .brc_img:after{width:1px;height:87px;background:linear-gradient(to top,#fff,#e5e5e5,#fff);display: block;content:"";}
        </style>
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody();?>
    <div class="ub-apj tx-c ulev14 fc666">
        <div class="w33 bbc_img click_true1">
            <div class="ub-apj brc_img">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_travel.png" width="35%" class="dblock center">
                    <div class="mt10">智慧旅游</div>
                </div>
            </div>
        </div>
        <div class="w33 bbc_img click_true2">
            <div class="ub-apj brc_img">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_wc.png" width="35%" class="dblock center">
                    <div class="mt10">智慧公厕</div>
                </div>
            </div>
        </div>
        <div class="w33 bbc_img click">
            <div class="ub-apj">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_businesscircles.png" width="35%" class="dblock center">
                    <div class="mt10">智慧商圈</div>
                </div>
            </div>
        </div>
    </div>
    <div class="ub-apj tx-c ulev14 fc666">
        <div class="w33 bbc_img click">
            <div class="ub-apj brc_img">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_life.png" width="35%" class="dblock center">
                    <div class="mt10">智慧生活</div>
                </div>
            </div>
        </div>
        <div class="w33 bbc_img click">
            <div class="ub-apj brc_img">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_citymanaging.png" width="35%" class="dblock center">
                    <div class="mt10">智慧城管</div>
                </div>
            </div>
        </div>
        <div class="w33 bbc_img click">
            <div class="ub-apj">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_traffic.png" width="35%" class="dblock center">
                    <div class="mt10">智慧交通</div>
                </div>
            </div>
        </div>
    </div>
    <div class="ub-apj tx-c ulev14 fc666">
        <div class="w33 bbc_img click">
            <div class="ub-apj brc_img">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_medical.png" width="35%" class="dblock center">
                    <div class="mt10">智慧医疗</div>
                </div>
            </div>
        </div>
        <div class="w33 bbc_img click">
            <div class="ub-apj brc_img">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_governmentaffairs.png" width="35%" class="dblock center">
                    <div class="mt10">智慧政务</div>
                </div>
            </div>
        </div>
        <div class="w33 bbc_img click">
            <div class="ub-apj">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_logistics.png" width="35%" class="dblock center">
                    <div class="mt10">智慧物流</div>
                </div>
            </div>
        </div>
    </div>
    <div class="ub-apj tx-c ulev14 fc666">
        <div class="w33 bbc_img click">
            <div class="ub-apj brc_img">
                <div class="ub-f1 ptb20">
                    <img src="/img/lvyou/weixintoilet/zhtc_education.png" width="35%" class="dblock center">
                    <div class="mt10">智慧教育</div>
                </div>
            </div>
        </div>
    </div>
    <div class="textTips uhide"><span></span></div>
    <?php $this->endBody();?>

    </body>
    <script>
        $('.click').on('click',function(){
            textTip('暂未开通，敬请期待！');
        });
        $('.click_true1').on('click',function(){
            location.href="https://sq.wmqt.net/phone/";
        });
        $('.click_true2').on('click',function(){
            location.href="https://sq.wmqt.net/phone/toilet/";
        });
    </script>
</html>
<?php $this->endPage();?>
