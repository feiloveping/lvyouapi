<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\toilet\models\Toilet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="toilet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'webid')->textInput() ?>

    <?= $form->field($model, 'aid')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seotitle')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shownum')->textInput() ?>

    <?= $form->field($model, 'addtime')->textInput() ?>

    <?= $form->field($model, 'modtime')->textInput() ?>

    <?= $form->field($model, 'keyword')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tagword')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'litpic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ishidden')->textInput() ?>

    <?= $form->field($model, 'notice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'piclist')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'opentime')->textInput() ?>

    <?= $form->field($model, 'closetime')->textInput() ?>

    <?= $form->field($model, 'satisfyscore')->textInput() ?>

    <?= $form->field($model, 'usecount')->textInput() ?>

    <?= $form->field($model, 'lng')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'finaldestid')->textInput() ?>

    <?= $form->field($model, 'threetype')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'issmarty')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
