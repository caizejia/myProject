<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsIcBill;
use common\models\WmsIcBillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;
/**
 * IcBillController implements the CRUD actions for WmsIcBill model.
 */
class IcBillController extends CommonController
{
    public $modelClass = 'common\models\WmsIcBill';
}
