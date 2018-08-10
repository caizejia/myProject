<?php

namespace api\modules\v1\wms\controllers;

use yii;
use common\models\Common;
use common\models\WmsInventory1;
use yii\data\ActiveDataProvider;
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsInventory1Search;
use yii\helpers\ArrayHelper;
use PHPExcel;
use common\models\ExcelExport;
use common\models\WmsProductDetails;
use common\models\WmsSoBill;

//这里可以作为restfull 普通业务的例子参考
class InventoryController1 extends CommonController
{
    public $modelClass = 'common\models\WmsInventory1';
    
   
    public  function actions()
    {
        $actions = parent::actions();
        // 禁用"delete" 和 "create" 动作
//        unset($actions['delete']);
//        unset( $actions['create']);
//        unset($actions['update']);
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    { 
        $searchModel =  new WmsInventory1Search();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
//        print_r($dataProvider->getModels());die;
        return $dataProvider;
    }

    //建账（初始化库存）
    public function actionInititem()
    {
        $params = \Yii::$app->request->post();
        $product_details = new WmsProductDetails();
        //判断是否批量导入
        //是批量导入，处理文件
        if($params['file']){
            $file_dir = Yii::$app->getBasePath() . '/web/';        //下载文件存放目录
                //检查文件是否存在
                if (!file_exists($file_dir . $params['file'])) {
                    return [ 'code' => 400,'msg' => '文件找不到']; 
                } else {
                    //处理excel
                    $filePath = $file_dir . $params['file'];
                    $data = \moonland\phpexcel\Excel::import($filePath, [
                        'setFirstRecordAsKeys' => true,
                        'setIndexSheetByName' => true,
                        'getOnlySheet' => 'sheet1',
                    ]);
                    $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $datas = json_decode($json); 
                    $inv = new WmsInventory1();
                    foreach ($datas as $key => $value) { 
                        $good = $product_details::getGoodsId($value->sku);  
                        $ret = $inv->initItem($params['warehouse_id'],$good['id'] , $value->num );
                    }
                    return ["code"=> 200,"error"=> $ret];
                }

        }else{
            //不是批量导入
            $inv = new WmsInventory1();
            $ret = $inv->initItem($params['warehouse_id'],$params['goods_id'], $params['num']);
            if($ret){
                return ["code"=> 200,"error"=> ""];
            }else{
                return ["code"=> 500,"error"=> "入账失败"];
            }
        }
    }


    //添加 库存流水
    public function actionAddInventoryDetail()
    {
        $params = \Yii::$app->request->post();
        $inv = new WmsInventory1();
        
        switch ($params['ref_type']) {
            case '0'://库存建账
                $ret = $inv->initItem($params['warehouse_id'],$params['goods_id'], $params['num']);
                break;
            case '1'://采购入库
                $ret = $inv->inItem($params['warehouse_id'],$params['goods_id'], $params['num'],1);
                break;
            case '2'://采购退货出库
                $ret = $inv->outItem($params['warehouse_id'],$params['goods_id'], $params['num'],2);
                break;
            case '3'://销售出库
                $ret = $inv->outItem($params['warehouse_id'],$params['goods_id'], $params['num'],3);
                break;
            case '4'://销售退货入库
                $ret = $inv->inItem($params['warehouse_id'],$params['goods_id'], $params['num'],4);
                break;
            case '5'://库存盘点-盘盈入库
                $ret = $inv->inItem($params['warehouse_id'],$params['goods_id'], $params['num'],5);
                break;
            case '6'://库存盘点-盘亏出库
                $ret = $inv->outItem($params['warehouse_id'],$params['goods_id'], $params['num'],6);
                break;
            case '7'://库存调拨-出库
                $ret = $inv->outItem($params['warehouse_id'],$params['goods_id'], $params['num'],7);
                break;
            case '8'://库存调拨-入库
                $ret = $inv->inItem($params['warehouse_id'],$params['goods_id'], $params['num'],8);
                break; 
            case '11'://订单发货匹配-锁定库存
                $ret = $inv->lockItem($params['warehouse_id'],$params['goods_id'], $params['num']);
                break;
            case '12'://采购-增加在途库存
                $ret = $inv->inAfloat($params['warehouse_id'],$params['goods_id'], $params['num']);
                break;
            default:
                # code...
                break;
        }
 
        if($ret){
            return ["code"=> 200,"error"=> ""];
        }else{
            return ["code"=> 500,"error"=> "入账失败"];
        }
    }

    //库存调拨-出库', 从a库出库到b库
    public function actionOutItemInside()
    {
        $params = \Yii::$app->request->post();

        $inv = new WmsInventory1();
        $ret = $inv->outItemInside($params['warehouse_a_id'],$params['warehouse_b_id'],$params['goods_id'],$params['num'],$ref_type=7);
        if($ret){
            return ["code"=> 200,"error"=> ""];
        }else{
            return ["code"=> 500,"error"=> "出账失败"];
        }
    }

    //库存调拨-入库', 从a库入库到b库
    public function actionInItemInside()
    {
        $params = \Yii::$app->request->post();

        $inv = new WmsInventory1();
        $ret = $inv->inItemInside($params['warehouse_b_id'],$params['goods_id'],$params['num'],$ref_type=8);
        if($ret){
            return ["code"=> 200,"error"=> ""];
        }else{
            return ["code"=> 500,"error"=> "入账失败"];
        }
    }



    public function actionTest()
    {
        $searchModel =  new WmsInventory1Search();
        $params = \Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $datas = (new Common())->sortZdy($dataProvider,$params);
        return $datas;
    }

    //一般搜索
    public function actionSearch() { 
        return WmsInventory1::find()->where(['like','id',$_POST['keyword']])->all();
    }

    //以下操作可以方便测试人员，或者系统维护人员，直接启动后台任务
    //模拟发货匹配 ， 发货匹配是频率很高的启动，把可以发货的尽快发货。
    public function actionMocklock(){ 
        $orders = \Yii::$app->db->createCommand("select * from oms_order where status in (2)")->queryAll();
        $oms_order = new WmsSoBill();
        $ret = false;
        foreach($orders as $order)
        {
            Yii::warning('匹配发货 正在处理订单id:'.$order['id'] , __METHOD__);
            $ret =  $oms_order->matchInventory($order);
        }

        if($ret){
            Yii::warning('匹配发货 完成订单id:'.$order['id'] , __METHOD__);
            return ["code"=> 200,"error"=> ""];
        }else{
            Yii::warning('匹配发货 失败！！！  ' , __METHOD__);
            return ["code"=> 500,"error"=> ""];
        }
    }
    //模拟采购触发，  采购触发是频率相对低的启动，把不能匹配发货的积累采购。
    public function actionMockpurchase(){
        return '正在编码';
        $orders = Yii::$app->db->createCommand("select * from oms_order where status in (2)")->queryAll();
        $wms_purchases = new \common\models\WmsPurchases();
        foreach($orders as $order)
        {
            $ret = $wms_purchases->purchaseOrder($order);
        }
        if($ret){
            return ["code"=> 200,"error"=> ""];
        }else{
            return ["code"=> 500,"error"=> ""];
        }
    }
    //模拟匹配转运，  转运匹配是第一启动的，尽快转运发货。
    public function actionMockstock(){

        $orders = \Yii::$app->db->createCommand("select * from oms_order where status in (2)")->queryAll();
        $oms_order = new WmsSoBill();
        foreach($orders as $order)
        {
            Yii::warning('匹配转运 开始处理订单id:'.$order['id'] , __METHOD__);
            $ret = $oms_order->updateStock($order);
        }
        if($ret){
            return ["code"=> 200,"error"=> ""];
        }else{
            return ["code"=> 500,"error"=> ""];
        }
    }



    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
