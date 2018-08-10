<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsSrBill;
use common\models\WmsSrBillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;

/**
 * SrBillController implements the CRUD actions for WmsSrBill model.
 */
class SrBillController extends CommonController
{
    public $modelClass = 'common\models\WmsSrBill';


}
