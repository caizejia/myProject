<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WmsTransfer */

$this->title = 'Update Wms Transfer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wms Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="wms-transfer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
