<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\toilet\models\ToiletSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toilet-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'webid') ?>

    <?= $form->field($model, 'aid') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'seotitle') ?>

    <?php // echo $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'shownum') ?>

    <?php // echo $form->field($model, 'addtime') ?>

    <?php // echo $form->field($model, 'modtime') ?>

    <?php // echo $form->field($model, 'keyword') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'tagword') ?>

    <?php // echo $form->field($model, 'litpic') ?>

    <?php // echo $form->field($model, 'ishidden') ?>

    <?php // echo $form->field($model, 'notice') ?>

    <?php // echo $form->field($model, 'piclist') ?>

    <?php // echo $form->field($model, 'opentime') ?>

    <?php // echo $form->field($model, 'closetime') ?>

    <?php // echo $form->field($model, 'satisfyscore') ?>

    <?php // echo $form->field($model, 'usecount') ?>

    <?php // echo $form->field($model, 'lng') ?>

    <?php // echo $form->field($model, 'lat') ?>

    <?php // echo $form->field($model, 'finaldestid') ?>

    <?php // echo $form->field($model, 'threetype') ?>

    <?php // echo $form->field($model, 'issmarty') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
