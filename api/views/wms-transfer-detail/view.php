<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WmsTransferDetail */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wms Transfer Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-transfer-detail-view">

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
            'sku',
            'goods_count',
            'outqty',
            'inqty',
            'goods_money',
            'goods_price',
            'ws_bill_id',
            'memo',
        ],
    ]) ?>

</div>
