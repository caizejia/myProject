<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\WmsOtherSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wms Others';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-other-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Wms Other', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ref',
            'action_time',
            'action_user_id',
            'warehouse_id',
            //'hzfrom_id',
            //'status',
            //'remark',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
