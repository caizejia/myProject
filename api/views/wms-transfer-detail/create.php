<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WmsTransferDetail */

$this->title = 'Create Wms Transfer Detail';
$this->params['breadcrumbs'][] = ['label' => 'Wms Transfer Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-transfer-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
