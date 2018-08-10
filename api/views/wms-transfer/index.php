<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\WmsTransferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wms Transfers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-transfer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Wms Transfer', ['create'], ['class' => 'btn btn-success']) ?>
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
            'wfrom_id',
            //'wto_id',
            //'hzfrom_id',
            //'hzto_id',
            //'status',
            //'outstatus',
            //'instatus',
            //'remark',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
