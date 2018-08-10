<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WmsOther */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wms-other-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ref')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action_time')->textInput() ?>

    <?= $form->field($model, 'action_user_id')->textInput() ?>

    <?= $form->field($model, 'warehouse_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hzfrom_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
