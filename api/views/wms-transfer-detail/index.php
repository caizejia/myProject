<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\WmsTransferDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wms Transfer Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-transfer-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Wms Transfer Detail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sku',
            'goods_count',
            'outqty',
            'inqty',
            //'goods_money',
            //'goods_price',
            //'ws_bill_id',
            //'memo',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
