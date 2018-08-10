<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WmsTransferDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wms-transfer-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_count')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'outqty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inqty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ws_bill_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'memo')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
