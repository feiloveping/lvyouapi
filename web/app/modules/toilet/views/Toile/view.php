<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\toilet\models\Toilet */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Toilets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="toilet-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'webid',
            'aid',
            'title',
            'seotitle',
            'content:ntext',
            'address',
            'shownum',
            'addtime',
            'modtime',
            'keyword',
            'description',
            'tagword',
            'litpic',
            'ishidden',
            'notice',
            'piclist',
            'opentime',
            'closetime',
            'satisfyscore',
            'usecount',
            'lng',
            'lat',
            'finaldestid',
            'threetype',
            'issmarty',
        ],
    ]) ?>

</div>
