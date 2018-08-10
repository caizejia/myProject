<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsStocks;
use common\models\WmsStocksSearch;
use common\models\WmsShipServices;
use common\models\WmsSoBill; 
use common\models\WmsOrderPackageWz; 
use common\models\WmsProductDetails;
use api\modules\v1\wms\controllers\CommonController; 


/**
 * 这里是关于转运仓的
 */
class StocksController extends CommonController
{
    public $modelClass = 'common\models\WmsStocks';

    public  function actions()
    {
        $actions = parent::actions(); 
         
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    {
        $searchModel =  new WmsStocksSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams); 
        return $dataProvider;
    }



    //////////////////////////////////////////以下为转运仓收货//////////////////////////////////////////////
    //1，海外退件-检查物流单号（收货）
    public function actionReturnsLcCheck(){
        //$id = Yii::$app->request->post('id');
        $lc_number = Yii::$app->request->post('lc_number');
        $ShipServices = new WmsShipServices();
        $order = WmsSoBill::find()->andWhere(['=','lc_number',$lc_number])->one();
        if($order){
            if(($ShipServices->getLcV)[Yii::$app->user->id] == $order->lc){
                return [
                    'code' => 200,
                    'msg' => '正确',
                ];
            }else{
                return [
                    'code' => 500,
                    'msg' => '该单号不是本服务商的',
                ];
            }
        }else{
            return [
                'code' => 500,
                'msg' => '该物流单号不存在',
            ];
        }
    }
 

    //2，海外退件-扫描原运单号上架（收货）
    public function actionReturnsLcSubmit(){
        $location = Yii::$app->request->post('location');
        $lc_number = Yii::$app->request->post('lc_number');
        $lc_number = explode("\n", trim($lc_number));
        $reservoir_area = ((new WmsShipServices())->getLcV)[Yii::$app->user->id]; 
        $adminUser = new Adminuser();
        $res = [];
        foreach ($lc_number as $v){
            $v = trim($v);
            $order = WmsSoBill::find()->andWhere(['=','lc_number',$v])->one();
            if($order){ 
                $wms_product_details = new WmsProductDetails();
                $product = $wms_product_details::getFullInfo($order['website']);
                $stock = WmsStocks::find()->andWhere(['=','track_number',$v])->one();

                if(!$stock) {
                    Yii::$app->db->createCommand()->insert("wms_stocks",[
                        'order_id' => $order['id'],
                        'sku' => $product->sku_code,
                        'qty' => $order['qty'],
                        'create_date' => date('Y-m-d'),
                        'expired_day' => 30,
                        'status' => 0,
                        'fee' => 0,
                        'county' => $order['country'],
                        'sales' => $adminUser->findIdentity($product->ads_user)['realname'],
                        'track_company' => $order['lc'],
                        'track_number' => $order['lc_number'],
                        'reservoir_area' => $reservoir_area,
                        'location' => $location,
                    ])->execute();
                }

                if (!$order['status'] == '拒签') {
                    $order->setOrderStatus($order['id'],$order['status'],'13','海外仓导入改为拒签', Yii::$app->user->id); //状态改为 '13' => '拒签',
                }  

                Yii::$app->db->createCommand()->update('wms_stocks', [
                    'reservoir_area' => $reservoir_area,
                    'location' => $location,
                ], "order_id = '{$order['id']}'")->execute();
            }else{
                $res[$v] = false;
            }
        }


        if(in_array(false,$res)){
            return [
                'res' => false,
                'info' => $res,
                'msg' => '不完全通过',
            ];
        }else{
            return [
                'res' => true,
                'msg' => '全部通过',
            ];
        }
    }










    //////////////////////////////////////////以下为转运仓出货//////////////////////////////////////////////
    //1，海外退件-转运仓库存（出货）
    public function actionStock(){ 
        $reservoir_area = ((new WmsShipServices())->getLcV)[Yii::$app->user->id];
        $stocts = Yii::$app->db->createCommand("select * from wms_stocks where reservoir_area = '{$reservoir_area}' AND new_order_id > 0 AND status = '1'")->queryAll();
        return $this->wrapData($stocts);
    }

    //2，海外退件-转运仓 申请物流单号（出货）
    //扫描旧单号，得到新单号，称重复制 ，申请物流单号，改变转运状态，记录日志
    public function actionChoose(){
        $lc_number_old = Yii::$app->request->post('lc_number');
        if(trim($lc_number_old)){
            //1 扫描单号是否及格
            $order_old = WmsSoBill::find()->andWhere(['=','lc_number',$lc_number_old])->one();
            $order_id_new = Yii::$app->db->createCommand("select * from wms_stocks where new_order_id > 0 AND status = '1' and track_number = '{$lc_number_old}'")->queryOne();
            if(!$order_id_new){
                return json_encode(['code' => 500, 'msg' => '信息错误，禁止打印', 'audio' => '']);
            }
            //2 称重复制
            $order = WmsSoBill::findOne($order_id_new['new_order_id']);
            $order->lc = $order_old->lc;
            $order_package_wz_old = Yii::$app->db->createCommand("select * from wms_order_package_wz where order_id = '{$order_old->id}'")->queryOne();
            Yii::$app->db->createCommand()->insert("wms_order_package_wz",[
                'order_id' => $order->id,
                'weight' => $order_package_wz_old['weight'],
                'length' => $order_package_wz_old['length'],
                'width' => $order_package_wz_old['width'],
                'height' => $order_package_wz_old['height'],
            ])->execute();
            $weight = $order_package_wz_old['weight'];
            $orderPackage = WmsOrderPackageWz::find()->andWhere(['=','order_id',$order->id])->one();

            //3 申请物流单号
            $WmsShipServices = new WmsShipServices();
            $ship_return = $WmsShipServices->build($order_id_new['new_order_id']);//物流申请单号

            //4 改变转运状态，
            if($ship_return['shipping_ok'] = true){
                Yii::$app->db->createCommand()->update('wms_stocks', [
                    'status' => 5,
                    'print_time' => date('Y-m-d H:i:s'),
                ], "track_number = '{$lc_number_old}'")->execute();
            }
        }
    }







    //3，海外退件-转运仓库存 待出库（出货）
    public function actionOutbound(){
        $reservoir_area = ((new WmsShipServices())->getLcV)[Yii::$app->user->id];
        $stocts = Yii::$app->db->createCommand("select * from wms_stocks where reservoir_area = '{$reservoir_area}' AND new_order_id > 0 AND status = '5'")->queryAll();
        return $this->wrapData($stocts);
    }

    //4，外退件-转运仓库存 出库（出货）
    //扫描新单号，确认出库
    public function actionChuKu(){
        $lc_number = trim(Yii::$app->request->post('lc_number'));
        $order = WmsSoBill::find()->andWhere(['=','lc_number',$lc_number])->one(); 
        if($order){
            if(Yii::$app->db->createCommand()->update('stocks', ['status' => 6 ,'outbound_time'=>date('Y-m-d H:i:s')], "new_order_id = '{$order['id']}'")->execute()){

                $order->setOrderStatus($order['id'],$order['status'],'8','外退件-转运仓库存 出库', Yii::$app->user->id); //状态改为 '8' => '已发货',

                return ['code' => 200, 'msg' => '出库成功'];
            }else{
                return ['code' => 500, 'msg' => '出库失败'];
            }
        }else{
            return ['code' => 500, 'msg' => '找不到该订单'];
        }
    }

    //5，外退件-转运仓库存 已出库列表（出货）
    public function actionOutboundList()
    { 
        $searchModel = new WmsStocksSearch();
        $model = new WmsStocks();
        $searchModel->status = 6;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $dataProvider;
    }


 
}
