<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WmsTransfer */

$this->title = 'Create Wms Transfer';
$this->params['breadcrumbs'][] = ['label' => 'Wms Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-transfer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
