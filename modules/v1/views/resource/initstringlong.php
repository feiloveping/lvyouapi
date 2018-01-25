<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/22
 * Time: 15:16
 */
use app\assets\InitString;
InitString::register($this)
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
    <title>Title</title>
    <style>
        .title{
            font-size:1rem;
        }
    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<p class="title"></p>
<div id="content"><?php echo $strings ;?></div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
