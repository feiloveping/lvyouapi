<?php

use app\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>详情</title>
    <style>
        #header{padding:15px;border-bottom:1px solid #dedede;}
        #title{font-size:20px;font-weight: 600}
        #date{margin-top:15px;}
        #content{padding:0;}
        .content{padding:15px;}
    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div id="content">
    <!--标题-->
    <div id="header">
        <p id="title"></p>
        <p id="date"></p>
    </div>
    <!--内容载体-->
    <div class="content"></div>
</div>
<!--js-->
<script>
    if(toJson().type=="articledetail"){
        $.ajax({
            url:_https+'v1/articledetail/'+toJson().id,
            type:'GET',
            success:function(data){
                $("#title").html(data.data.title);
                $("#date").html(getLocalTime(data.data.modtime));
                $(".content").html(data.data.content)
                $("img").css({width:'100%'})
            }
        })
    }else if(toJson().type=="notesdetail"){
        $.ajax({
            url:_https+'v1/notesdetail/'+toJson().id,
            type:'GET',
            success:function(data){

                $("#title").html(data.data.title);
                $("#date").html(getLocalTime(data.data.modtime));
                $(".content").html(data.data.content)
                $("img").css({width:'100%'})
            }
        })
    }
    function getLocalTime(nS) {
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
    }

</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
