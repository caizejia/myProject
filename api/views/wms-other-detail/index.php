<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\WmsOtherDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wms Other Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-other-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Wms Other Detail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sku',
            'outqty',
            'goods_money',
            'goods_price',
            //'ws_bill_id',
            //'memo',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
