<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WmsOther */

$this->title = 'Create Wms Other';
$this->params['breadcrumbs'][] = ['label' => 'Wms Others', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wms-other-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
