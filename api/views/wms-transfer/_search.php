<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WmsTransferSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wms-transfer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ref') ?>

    <?= $form->field($model, 'action_time') ?>

    <?= $form->field($model, 'action_user_id') ?>

    <?= $form->field($model, 'wfrom_id') ?>

    <?php // echo $form->field($model, 'wto_id') ?>

    <?php // echo $form->field($model, 'hzfrom_id') ?>

    <?php // echo $form->field($model, 'hzto_id') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'outstatus') ?>

    <?php // echo $form->field($model, 'instatus') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
