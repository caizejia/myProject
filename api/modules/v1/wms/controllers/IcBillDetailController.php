<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsIcBillDetail;
use common\models\WmsIcBillDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;
/**
 * IcBillDetailController implements the CRUD actions for WmsIcBillDetail model.
 */
class IcBillDetailController extends CommonController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = 'common\models\WmsIcBillDetail';

    public function actionBill($id){

//        return  WmsIcBillDetail::find()->where(['ic_bill_id'=>$id])->asArray()->all();
        return  WmsIcBillDetail::find()->where(['ic_bill_id'=>$id])->all();


    }

}
