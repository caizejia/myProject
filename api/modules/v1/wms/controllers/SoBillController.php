<?php
namespace api\modules\v1\wms\controllers;

use yii;
use yii\data\ActiveDataProvider; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsSoBill; 
use common\models\WmsSoBillDetail; 
use common\models\WmsOrderPackageWz;
use common\models\WmsSubInventorySku;
use common\models\WmsInventory1;
use common\models\WmsPickRoundDetail; 
use common\models\WmsShipServices;
use common\models\WmsProduct;
use common\models\WmsProductDetails;

//这里可以作为restfull 普通业务的例子参考
class SoBillController extends CommonController
{
    public $modelClass = 'common\models\WmsSoBill';

    public  function actions()
    {
        $actions = parent::actions();
        // 禁用"delete" 和 "create" 动作
        unset($actions['delete']);
        unset( $actions['create']);
        unset($actions['update']);
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    //测试 setOrderStatus 函数
    public function actionTest(){
        $WmsSoBill = new WmsSoBill();
        $ret = $WmsSoBill->setOrderStatus($order_id='1',$before_status='1',$after_status='2',$remark='系统自动执行波次，修改状态为拣货中',$user_id=0);
        var_dump($ret);exit;
    }

    //审核订单打包数量
    public function actionCheck()
    {
    	$ret = ['code'=>0, 'message'=>'不匹配']; 
        if(isset($_POST['order_no']) AND isset($_POST['skus'])){ 
        	$order = WmsSoBill::findOne(['id' => $_POST['order_no']] );
            $details = $order->details;
            $pass_sku = json_decode($_POST['skus'],1); //这里默认是对方把sku*num 按json 格式传播

            //真实订单sku*num 情况，作为对比和返回
            $base = [];
            foreach ($details as $key => $value) {
                $base[$value['sku_code']] = $value['number'];
            }

            if(count($details)!=count($pass_sku)){
               return $ret = ['code'=>0, 'message'=>'sku类型不匹配','base'=>json_encode($base)]; 
            }

            foreach ($base as $k => $v) {
                if($pass_sku[$k]==$v){
                    $ret = ['code'=>1, 'message'=>'匹配','base'=>json_encode($base)]; 
                }else{
                    return $ret = ['code'=>0, 'message'=>'订单的sku数量不匹配','base'=>json_encode($base)]; 
                }
            }
             
        }
        
 
        return $ret;
    }

 
    /**
     * 录入包裹尺寸
     * @return string
     */
    public function actionSize()
    {
        $id = \Yii::$app->request->post('id');
        $length = \Yii::$app->request->post('length');
        $width = \Yii::$app->request->post('width');
        $height = \Yii::$app->request->post('height');
        if ($id) {
            $model = new WmsSoBill();
            $order = $model->findOne($id);
            if ($order) {
                $orderPackage = new WmsOrderPackageWz();
                $package = $orderPackage->find()->where(['order_id' => $id])->one();
                if (!$package) {
                    $orderPackage->attributes = [
                        'order_id' => $id,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,
                    ];
                } else {
                    $orderPackage = $package;
                    $orderPackage->length = $length;
                    $orderPackage->width = $width;
                    $orderPackage->height = $height;
                }

                if ($orderPackage->save()) {
                    return ['code'=>200,'message'=>'ok'] ;
                } else {
                    return ['code'=>500,'message'=>'保存失败'] ;
                }
            } else {
                return ['code'=>404,'message'=>'订单不存在'] ;
            }
        } else {
            return ['code'=>404,'message'=>'订单id不存在'] ;
        } 
    }


     /**
     * 订单扫描 ，确认出货
     * 输入 订单号， 
     * 输出成功与否
     */
    public function actionOut()
    {
        $order_no = \Yii::$app->request->post('order_no');
        $warehouse_id = \Yii::$app->request->post('warehouse_id');
        $order = WmsSoBill::findOne(['id' => $order_no] ); 
        $order_details = $order->details;
        if(empty($order) OR empty($warehouse_id)){
            return ['code'=>404,'message'=>'订单不存在'] ;
        }

        //1，如果出库前没有记录 (库区的出库信息） 的上传数据流程，那么就要执行下面这一步，如果有，那么就注释掉 第一步。
        //库位 数据变动
        //记录库位变动日志     
        //从拣货单得到 库区库位 变动信息
        $WmsPickRoundDetail = new WmsPickRoundDetail(); 
        $pick_info = $WmsPickRoundDetail->find()->where(['id' => $order_no])->one();
        $pick_order_info = json_decode($pick_info['order_info'],1);
        $WmsSubInventorySku = new WmsSubInventorySku();
        if($pick_order_info){
            foreach ($pick_order_info as $key => $info) {
                $ret = $WmsSubInventorySku->in_or_out($info['sku_code'],$info['sub_inventory_id'],$info['number'],$type=2);
                if(!$ret){
                    return ['code'=>500,'message'=>'发货失败，库位库存不足。请联系管理员检查原因。'] ;
                }
            }
        }
        
       
        //2,sku的库存 变动
        $WmsInventory = new WmsInventory1();
        foreach ($order_details as $key => $detail) {
            $ret = $WmsInventory->outItem($warehouse_id,$detail['pid'], $detail['number'],$ref_type=3); //TODO $detail['pid']不存在，对应产品sku
            if(!$ret){
                return ['code'=>500,'message'=>'发货失败，总库存不足。请联系管理员检查原因。'] ;
            }
        }
        
        //3,订单状态变动
        $WmsSoBill = new WmsSoBill();
        $ret = $WmsSoBill->setOrderStatus($order['id'],$order['status'],$after_status='8',$remark='修改状态为已发货',$user_id=\Yii::$app->user->id);
        if($ret){
            return ['code'=>200,'message'=>'ok'] ;
        } else {
            return ['code'=>500,'message'=>'发货失败，修改订单状态失败。请联系管理员检查原因。'] ;
        }
    }


     /**
     * 包裹重量录入接口
     */
    public function actionWeight()
    { 
        $id = \Yii::$app->request->post('id');
        $weight = \Yii::$app->request->post('weight');
        if ($id) {
            $model = new WmsSoBill();
            $order = $model->findOne($id);
            //TODO 状态改为数字
            if(!$order){
                return ['code' => 500, 'msg' => '警告：订单不存在', 'audio' => 'cancel.mp3'] ; 
            }
            if ('已取消' == $order->status) {
                return ['code' => 500, 'msg' => '警告：订单已取消', 'audio' => 'cancel.mp3'] ; 
            }
            if ('已打包' == $order->status || '已发货' == $order->status) {
                return ['code' => 500, 'msg' => '警告：订单状态为【' . $order->status . '】可能重复发货', 'audio' => 'reshipping.mp3']; 
            }
            if($order->status != '捡货中'){
                return ['code' => 500, 'msg' => '警告：订单状态为【' . $order->status . '】，不能称重', 'audio' => 'reshipping.mp3']; 
            }
            $shipping_ok = false;
            if ($order) {
                if (empty($order->lc)) {
                    $order->updateTrackName();
                }
                //1，保存重量
                $orderPackage = new WmsOrderPackageWz();
                $package = $orderPackage->find()->where(['order_id' => $id])->one();
                if (!$package) {
                    $orderPackage->attributes = [
                        'order_id' => $id,
                        'weight' => $weight
                    ];
                } else {
                    $orderPackage = $package;
                    $orderPackage->weight = $weight;
                }
                //2，确定物流，申请单号
                if ($orderPackage->save()) {
                    \Yii::$app->db->createCommand()->update('orders', ['shipping_date' => date('Y-m-d H:i:s')], "id = {$id}")->execute();

                    $WmsShipServices = new WmsShipServices();
                    $ship_return = $WmsShipServices->build($id);//物流申请单号

                    return $ship_return;

                } else {
                    return ['code' => 500, 'msg' => '保存失败', 'audio' => ''];
                }
            } else {
                return ['code' => 500, 'msg' => '订单不存在', 'audio' => ''];
            }
        } else {
           return ['code' => 500, 'msg' => '订单不存在', 'audio' => ''];
        }
    }





    //生成条形码
    //TODO oms系统 产品表不确定 拣货 TODO TODO TODO
    public function actionCreateBarcode()
    {
        $country_id = [
            'MY' => '01',
            'SG' => '02',
            'TH' => '03'
        ];
        if (\Yii::$app->request->post('cods')) {

            $pdf = '<p>';
            $cods = explode("\n", \Yii::$app->request->post('cods'));
            foreach ($cods as $cod) {
                $cod = trim($cod);
                if ($cod) {
                    $order = WmsSoBill::findOne($cod);
                    $pdf .= '<div style="height: 98mm;width:98mm;padding: 1mm;margin-bottom: 2mm;">';
                    $logo = $order->county == 'ID' ? '' : '<img src="http://admin.orkotech.com/images/wowmall.jpg" height="79px">';
                    $pdf .= '<div><table><tr><td>'.$logo.'</td><td><img src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=16&text=' . $cod . '&thickness=30&start=C&code=BCGcode128"></td></tr></table></div>';
                    $pdf .= '<br/>';
                    $items = WmsSoBillDetail::findAll(['order_id' => $order->id]);
                    $product = Products::findOne($order->website); //TODO 
                    $product_type = $product->product_type == '普货' ? 'P' : 'M';
                    if ($items) {
                        $value = '';
                        if (in_array($product->classify, ['T', 'J', 'S'])) {
                            $title = 'HAPPY SHOPPING';
                        } else {
                            $title = $order->product;
                        }
                        foreach ($items as $item) {
                            if (in_array($product->classify, ['T', 'J', 'S'])) {
                                $value .= '<li>' . ' <span>QTY: ' . $item->qty . '</span><br>SKU:' . $item->sku . '</li>';
                            } else {
                                $value .= '<li>' . preg_replace('/尺寸/', 'Size', preg_replace('/颜色/', 'Style', preg_replace('/产品ID\:\d+/', '', $item->product))) . ' <span>QTY: ' . $item->qty . '</span><br>SKU:' . $item->sku . '</li>';
                            }

                        }
                        $pdf .= '<b>' . $title . '</b>' . '<ol style="padding: 0 0 0 15px;">' . $value . '</ol>';
                    } else {
                        $pdf .= $order->product . ' X ' . $order->qty;
                    }
                    if ($order->comment) {
                        $pdf .= 'Remarks: ' . $order->comment;
                    }
                    $pdf .= '<p style="text-align: right;font-weight: 600">' . $order->county . ' ' . $country_id[$order->county] . ' </p>';
                    $pdf .= '<p style="text-align: right;font-weight: 600">' . $product_type . ' </p>';
                    $pdf .= '</div>';
                }

            }
            $pdf .= '</p>';

            $mpdf = new mPDF('zh-CN', [100, 100], 0, '', 2, 2, 2, 2);
            $mpdf->useAdobeCJK = true;
            //原来的html页面
            $mpdf->WriteHTML($pdf);

            //保存名称
            $mpdf->Output('MyPDF', 'I');
        } else {
            return ['code'=>404,'message'=>'订单id不存在'] ;
        }
    }





    //以下是TODO

    
    /**
     * 拣货操作批量打印
     * @return mixed
     */
    public function actionPickPrintjhd()
    {
        $id = Yii::$app->request->get('id');
        $id = explode(',', $id);
        $country_id = [
            'MY' => '01',
            'SG' => '02',
            'TH' => '03'
        ];
        $pdf = '';
        foreach ($id as $cod) {

            $info = WmsSoBill::findOne($cod);  
            if (!in_array($info['status'], [5])) { //'待发货'
                $pdf .= '<h1>' . '该订单不是待发货或已被他（她）人采购，请刷新页面后重新操作，如问题还未解决，请联系管理员' . '</h1>';
            } else {
                $user = Yii::$app->user->id;
                if (empty($info['user_picking'])) {
                    //步骤a: 更新拣货信息
                    Yii::$app->db->createCommand()->update('oms_order', ['is_picked' => '1', 'user_picking' => $user], "id = {$cod}")->execute();

                    //步骤b: 设置订单信息
                    $ret = $info->setOrderStatus($cod,$info['status'],5,$remark='待发货打印',$user);//待发货

                     
                    $pdf .= '<div style="width: 200mm;height:200mm;">';
                    $logo = $info->country == 'ID' ? '' : '<img src="http://admin.orkotech.com/images/wowmall.jpg" height="79px">';
                    $pdf .= '<div><table><tr><td>' . $logo . '</td><td><img src="http://admin.orkotech.com/barcodegen/html/image.php?filetype=PNG&dpi=96&scale=2&rotation=0&font_family=Arial.ttf&font_size=16&text=' . $cod . '&thickness=30&start=C&code=BCGcode128"></td></tr></table></div>';
                    $pdf .= '<br/>';
                    $items = $info->details;
                    $product = WmsProduct::findOne($info->website); //TODO
                    if (in_array($product->classify, ['T', 'J', 'S'])) {
                            $title = 'HAPPY SHOPPING';
                    } else { 
                            $title = '';
                    }
                    $product_type = $product->product_type == '普货' ? 'P' : 'M';

                    if ($items) {
                            $value = '';
                            foreach ($items as $item) {
                                if (in_array($product->classify, ['T', 'J', 'S'])) {
                                    $value .= '<li>' . ' <span>QTY: ' . $item->number . '</span><br>SKU:' . $item->sku_code . '</li>';
                                } else {
                                    $value .= '<li>' . preg_replace('/尺寸/', 'Size', preg_replace('/颜色/', 'Style', preg_replace('/产品ID\:\d+/', '', $item->product))) . ' <span>QTY: ' . $item->number . '</span><br>SKU:' . $item->sku_code . '</li>';
                                }

                            }
                            $pdf .= '<b>' . $title . '</b>' . '<ol style="padding: 0 0 0 15px;">' . $value . '</ol>';
                    } else {
                            $pdf .= $title . ' X ' . $info->qty;
                    }

                    if ($info->comment) {
                            $pdf .= 'Remarks: ' . $info->comment;
                    }
                    $pdf .= '<p style="text-align: right;font-weight: 600"><b>' . $info->country . ' ' . $country_id[$info->country] . '</b></p>';
                    $pdf .= '<p style="text-align: right;font-weight: 600"><b>' . $product_type . '</b></p>';
                    $pdf .= '</div>'; 
                } 

            }
        }
        $mpdf = new mPDF('zh-CN', [100, 100], 0, '', 2, 2, 2, 2);
        $mpdf->useAdobeCJK = true;
        //原来的html页面
        $mpdf->WriteHTML($pdf);
        //保存名称
        $mpdf->Output('MyPDF', 'I');

    }





    /**
     * 拣货操作批量更新状态为捡货中
     * @return mixed
     */
    public function actionPickUpdate()
    {
        $id = Yii::$app->request->post('id');
        $id = explode(',', $id);
        $res = '';
        foreach ($id as $cod) { 
            $info = WmsSoBill::findOne($cod);   
            $user = Yii::$app->user->id;
            if (in_array($info['status'], ['5']) && $info['is_picked'] == '1') { //待发货=5
                if ($info['user_picking'] == $user) {


                    $ret = $info->setOrderStatus($cod,$info['status'],6,$remark='已打印改状态为捡货中',$user);//待发货  
 
                } else {
                    $adminUser = new AdminUser();
                    $userName = $adminUser->getNameById($info['user_picking']);
                    $res .= $cod . '该订单是由 ' . $userName . ' 打印，您无权操作' . ' ';
                }
            } elseif ($info['status'] == '待发货' && $info['is_picked'] != '1') {
                $res .= $cod . '未打印不能修改状态' . ' ';
            } elseif ($info['status'] == '捡货中') {
                $res .= $cod . '状态已是拣货中' . ' ';
            } else {
                $res .= $cod . '状态错误,请联系管理员' . ' ';
            }
        }
        return ['status'=>500,'msg'=>$res];  
    }


    /**
     * 修改订单状态（问题件）
     * @return mixed
     */
    public function actionProblems()
    {
        $info = Yii::$app->request->post();
        if (Yii::$app->request->post()) {
            $order_id = Yii::$app->request->post('order_id');
            $problem = Yii::$app->request->post('problem');
            $status = Yii::$app->request->post('status');
            $description = Yii::$app->request->post('description');
            $track_number = Yii::$app->request->post('track_number');
            $time = date('Y-m-d H:i:s');
            if (!(Problems::find()->where(['order_id' => $order_id])->one())) {
                Yii::$app->db->createCommand()->insert("problems", ["order_id" => $order_id, "problem" => $problem, "status" => $status, "description" => $description, "track_number" => $track_number, "create_date" => $time])->execute();
                $logModel = new OrderLogs();
                $logModel->attributes = [
                    'order_id' => $order_id,
                    'status' => $status,
                    'user_id' => Yii::$app->user->id,
                    'comment' => '问题件表不存在该订单，写入该订单, 问题：' . $problem . '描述：' . $description,
                ];
                $logModel->save();
            } else {
                Yii::$app->db->createCommand()->update('problems', ["problem" => $problem, "status" => $status, "description" => $description, "track_number" => $track_number, "create_date" => $time], "order_id = {$order_id}")->execute();
                $logModel = new OrderLogs();
                $logModel->attributes = [
                    'order_id' => $order_id,
                    'status' => $status,
                    'user_id' => Yii::$app->user->id,
                    'comment' => '问题件表存在该订单，更新该订单,问题：' . $problem . '描述：' . $description,
                ];
                $logModel->save();
            }
            Yii::$app->db->createCommand()->update('orders', ['status' => '问题件'], "id = {$order_id}")->execute();
            $logModel = new OrderLogs();
            $logModel->attributes = [
                'order_id' => $order_id,
                'status' => '问题件',
                'user_id' => Yii::$app->user->id,
                'create_date' => date('Y-m-d'),
                'comment' => '订单表更新该订单为问题件',
            ];
            $logModel->save();
            return '成功';
        }
        return '修改失败,请联系管理员';

    }
}
