<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsTransfer;
use common\models\WmsTransferSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;

/**
 * WmsTransferController implements the CRUD actions for WmsTransfer model.
 */
class WmsTransferController extends CommonController
{
    public $modelClass = 'common\models\WmsTransfer';
}
