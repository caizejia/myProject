<?php

namespace api\modules\v1\wms\controllers;

use common\models\WmsProductDetails;
use common\models\WmsReceivables;
use Yii;
use common\models\WmsPrBill;
use common\models\WmsPrBillSearch;  
use common\models\WmsPurchasesDetail;
use api\modules\v1\wms\controllers\CommonController; 

/**
 * 采购退货出库单
 */
class PrBillController extends CommonController
{
    public $modelClass = 'common\models\WmsPrBill';

    public  function actions()
    {
        $actions = parent::actions();
        
        unset($actions['index']);// 以下重写了原来的 index
        unset($actions['create']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    {
        $searchModel =  new WmsPrBillSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $dataProvider;
    }


    /**
     * 写 采购退货出库单 表
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $info = Yii::$app->request->post();
        if($info['rejection_goods_count']>0 and $info['purchases_detail_id']>0){
            $WmsPurchasesDetail = new WmsPurchasesDetail();
            $purchases = $WmsPurchasesDetail->find()->where(['id' => trim($info['purchases_detail_id'])])->one();
            //TODO 暂时没值
             

            $ret = Yii::$app->db->createCommand()->insert("wms_pr_bill", [
                "goods_id"              =>  $purchases['good_id'],
                "rejection_goods_count" =>  $info['rejection_goods_count'],
                "rejection_goods_price" =>  $purchases['price'],
                "rejection_money"       =>  $purchases['price']*$info['rejection_goods_count'],
                "status"                =>  0,
                "action_time"           =>  date("Y-m-d H:i:s"),
                "action_user_id"        =>  Yii::$app->user->id,
                "supplier_id"           =>  '',
                "warehouse_id"          =>  $info['warehouse_id'],
                "purchases_detail_id"   =>  $info['purchases_detail_id'],
                "pw_bill_detail_id"     =>  0
            ])->execute();

            if($ret){
                return ['code' => 200, 'msg' => '成功'];
            }else{
                return ['code' => 500, 'msg' => '失败'];
            }
        }
    }

    /**
     * 确认退货单
     * 流程： 确认退货，减少库存，修改采购单记录， 生成退款单
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionConfirm()
    {
        $info = Yii::$app->request->post();
        if($info['id'] > 0 and $info['status'] == '待确认'){
            //1,确认退货
            $after_status = array_search('待出库', WmsPrBill::$status);
            //确认退货单时间
            date_default_timezone_set("Asia/Shanghai");
            $action_time = date('Y-m-d H:i:s');
            \Yii::$app->db->createCommand()->update('wms_pr_bill', [
                'status' => $after_status,
                'action_time' => $action_time,
                'action_user_id' => Yii::$app->user->id
            ], "id = {$info['id']}")->execute();
            //2,减少库存 TODO 要判断是否已经入库的情况
 
            //3,修改采购单记录
            $WmsPrBill = new WmsPrBill();
            $PrBill = $WmsPrBill->find()->where(['id' => $info['id']])->one();
//            $purchases = WmsPurchasesDetail::findOne(trim($PrBill['purchases_detail_id']));
//            $ret = \Yii::$app->db->createCommand()->update('wms_purchases_detail', [
//                'minus_library_count' => $purchases->minus_library_count + $PrBill['rejection_goods_count']
//            ], "id = {$purchases->id}")->execute();

            //4,生成退款单  
            $ret = Yii::$app->db->createCommand()->insert("wms_receivables", [
                "ref"                   => 'Rc'.date('YmdHis'),
                "rv_money"              => $PrBill['rejection_money'], //应收账款
                "act_money"             => 0, //已收账款
                "balance_money"         => $PrBill['rejection_money'], //未收账款
                "supplier_id"           => $PrBill['supplier_id'],
                "create_time"           => date("Y-m-d H:i:s"),
                "purchases_detail_id"   => $PrBill['purchases_detail_id'],
                "pr_bill_id"            => $PrBill['id'],
                "status"                => 0
            ])->execute();

            if($ret){
                return ['code' => 200, 'msg' => '确认退货单成功'];
            }
        }
        return ['code' => 500, 'msg' => '失败'];
    }

    /**
     * 伪删除并记录日志
     * @throws \yii\db\Exception
     */
    public function actionDel(){
        $id = Yii::$app->request->post('id');
        if($id = trim($id)){
            //伪删除
            $res1 = \Yii::$app->db->createCommand()->update('wms_receivables', [
                'is_del' => 1
            ], "id = {$id}")->execute();
            //写日志
            $res2 = Yii::$app->db->createCommand()->insert("wms_receiving_log", [
                "act_money"         =>  '',
                "action_user_id"    =>  Yii::$app->user->id,
                "pay_user_id"       =>  '',
                "pay_to_user_id"    =>  '',
                "bill_id"           =>  '',
                "ref"               => '',
                "memo"              => '',

            ])->execute();
        }
    }
}
