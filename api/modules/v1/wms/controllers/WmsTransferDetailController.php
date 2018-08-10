<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsTransferDetail;
use common\models\WmsTransferDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;

/**
 * WmsTransferDetailController implements the CRUD actions for WmsTransferDetail model.
 */
class WmsTransferDetailController extends CommonController
{
    public $modelClass = 'common\models\WmsTransferDetail';

    public function actionBill($id){
        return WmsTransferDetail::find()->where(['ws_bill_id'=>$id])->all();
    }
}
