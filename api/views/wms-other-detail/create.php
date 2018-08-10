<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WmsOtherDetail */

$this->title = 'Create Wms Other Detail';
$this->params['breadcrumbs'][] = ['label' => 'Wms Other Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-other-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
