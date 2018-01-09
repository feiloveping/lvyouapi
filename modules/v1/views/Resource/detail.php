<?php

use app\assets\AppAsset;
AppAsset::register($this);

/* @var $this \yii\web\View */
/* @var $content string */

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<!--内容载体-->
<div id="content"></div>
<!--js-->
<script>
    if(toJson().type=="articledetail"){
        $.ajax({
            url:_https+'v1/articledetail/'+toJson().id,
            type:'GET',
            success:function(data){
                $("#content").html(data.data.content)
                $("img").css({width:'100%'})
            }
        })
    }else if(toJson().type=="notesdetail"){
        $.ajax({
            url:_https+'v1/notesdetail/'+toJson().id,
            type:'GET',
            success:function(data){
                $("#content").html(data.data.content)
                $("img").css({width:'100%'})
            }
        })
    }
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
