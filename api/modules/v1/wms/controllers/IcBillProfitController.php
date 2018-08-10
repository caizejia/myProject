<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsIcBillProfit;
use common\models\WmsIcBillProfitSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;
/**
 * IcBillProfitController implements the CRUD actions for WmsIcBillProfit model.
 */
class IcBillProfitController extends CommonController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = 'common\models\WmsIcBillProfit';
}
