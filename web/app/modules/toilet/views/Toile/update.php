<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\toilet\models\Toilet */

$this->title = 'Update Toilet: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Toilets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="toilet-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
