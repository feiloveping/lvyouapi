<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\toilet\models\Toilet */

$this->title = 'Create Toilet';
$this->params['breadcrumbs'][] = ['label' => 'Toilets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toilet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
