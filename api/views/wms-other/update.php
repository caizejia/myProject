<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WmsOther */

$this->title = 'Update Wms Other: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wms Others', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="wms-other-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
