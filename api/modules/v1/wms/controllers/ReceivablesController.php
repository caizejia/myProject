<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsReceivables;
use common\models\WmsReceivablesSearch;
use common\models\WmsPrBill;
use api\modules\v1\wms\controllers\CommonController; 

/**
 * 采购退款单
 */
class ReceivablesController extends CommonController
{
    public $modelClass = 'common\models\WmsReceivables';

    public  function actions()
    {
        $actions = parent::actions();
        
        unset($actions['index']);// 以下重写了原来的 index
        unset($actions['create']);
        return $actions;
    }

    
    public function actionIndex()
    {
        $searchModel =  new WmsReceivablesSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams); 
        return $dataProvider;
    }

    /**
     * 对  退货单的灵活处理  不从采购单里面读取金额而是写传来的金额
     * 退货单-》生成退款单
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $info = Yii::$app->request->post();
        if(  $info['pr_bill_id']>0 and $info['rv_money']>0){
            $PrBill = WmsPrBill::findOne(trim($info['pr_bill_id']));
            $ret = Yii::$app->db->createCommand()->insert("wms_receivables", [
                "ref"                   => 'Rc'.date('YmdHis'),
                "rv_money"              => $info['rv_money'],
                "act_money"             => 0,
                "balance_money"         => $info['rv_money'],
                "supplier_id"           => 0,
                "action_time"           => date("Y-m-d H:i:s"),
                "action_user_id"        => Yii::$app->user->id,
                "purchases_detail_id"   => $PrBill['purchases_detail_id'],
                "pr_bill_id"            => $info['pr_bill_id'],
                "status"                => 0
            ])->execute();

            if($ret){
                return ['code' => 200, 'msg' => '成功'];
            }else{
                return ['code' => 500, 'msg' => '失败'];
            }
        }
    }
}
