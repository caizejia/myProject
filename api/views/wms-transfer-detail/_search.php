<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WmsTransferDetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wms-transfer-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sku') ?>

    <?= $form->field($model, 'goods_count') ?>

    <?= $form->field($model, 'outqty') ?>

    <?= $form->field($model, 'inqty') ?>

    <?php // echo $form->field($model, 'goods_money') ?>

    <?php // echo $form->field($model, 'goods_price') ?>

    <?php // echo $form->field($model, 'ws_bill_id') ?>

    <?php // echo $form->field($model, 'memo') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
