<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsOtherDetail;
use common\models\WmsOtherDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;

/**
 * WmsOtherDetailController implements the CRUD actions for WmsOtherDetail model.
 */
class WmsOtherDetailController extends CommonController
{
    public $modelClass = 'common\models\WmsOtherDetail';

    public function actionBill($id){

        return WmsOtherDetail::find()->where(['ws_bill_id'=>$id])->all();
    }
}
