<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/4
 * Time: 11:12
 */

namespace api\modules\v1\wms\controllers;

use common\models\WmsPayments;
use yii;
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsPurchases;
use common\models\WmsPurchasesDetailSearch;
use common\models\Adminuser;
use common\models\WmsProductDetails;
use common\models\WmsPurchasesDetail;

class PurchasesDetailController extends CommonController
{
    public $modelClass = 'common\models\WmsPurchasesDetail';

    /**
     * @return array
     * @重写设置
     */
    public function actions()
    {
        $actions = parent::actions();
        // 禁用"delete" 和 "create" 动作
//        unset($actions['delete']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $searchModel = new WmsPurchasesDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//print_r($dataProvider->getModels());die;

        if (empty($dataProvider->getModels())){
            return false;
        }

        $sum = 0;
        foreach ($dataProvider->getModels() as $value)
        {
            $model = new WmsPurchasesDetail();
            //当前操作人姓名
            $username = empty($value['action_user_id'])?'系统':Adminuser::getUsername($value['action_user_id']);
            //通过公共方法获取sku、image、attribute
            $cacheData = WmsProductDetails::getFullInfo($value['good_id']);
//            print_r($cacheData);die;
            if (!empty($cacheData['sku_code'])){
                $sku = trim($cacheData['sku_code']);
                //三天销量
                $threeDay = $model->getDaySalesVolume($sku,3);
                //七天销量
                $sevenDay = $model->getDaySalesVolume($sku,7);
            }
            //历史最低采购单价
            $lowest_price = $model->getLowestPrice($value['good_id']);
            //合计（产品单价*产品采购数量）
            $total = $value['price'] * $value['count'];
            //总和
            $sum += $total;
            //目前缺的数据：运费、币种、预计到货时间、3天销量、7天销量
            $data[] = [
                'id' => $value['id'],
                'purchase_id' => $value['purchase_id'],
                'ref' => $value['purchases']['ref'],  //系统采购单号
                'link' => $value['link'], //采购链接***
                'supplier_platform' => $value['purchases']['supplier_platform'],  //平台
                'supplier_ref' => $value['supplier_ref'],  //平台单号
                'logistics' => $value['logistics'], //物流单号***
                'supplier_name' => $value['purchases']['supplier_name'],  //供应商名称
                'create_time' => $value['purchases']['create_time'],  //采购订单生成时间
                'action_time' => $value['action_time'],  //采购时间
                'username' => $username,  //操作人
                'status' => WmsPurchasesDetail::$status[$value['status']],  //状态
                'image' => empty($cacheData['image'])?'':$cacheData['image'],  //详情图片
                'sku' => $sku,  //sku
                'size' => empty($cacheData['sku_attribute'])?'':$cacheData['sku_attribute'],  //属性：大小、尺寸
                'color' => empty($cacheData['sku_attribute'])?'':$cacheData['sku_attribute'],  //属性：颜色
                'three_day' => $threeDay,
                'seven_day' => $sevenDay,
                'lowest_price' => $lowest_price,
                'price' => $value['price'],  //当前产品单价***
                'should_count' => $value['should_count'],  //应采购的数量
                'count' => $value['count'],  //实际采购数量***
                'total' => $total,
                'memo' => $value['memo']
            ];

        }

//        $data['_sum'] = $sum;

        $data = $this->wrapData($data,$dataProvider->pagination);

        return $data;
    }

    /**
     * @return array|string
     * @throws yii\db\Exception
     * 新增采购详情
     */
    public function actionCreate()
    {
        //获取表单信息
        $post_data = Yii::$app->request->post();
        if (empty($post_data['purchase_id'])){
            return ['code' => 404, 'msg' => '找不到系统采购单号'];
        }
        //获取当前用户ID
        $user_id = Yii::$app->user->id;
        //获取sku表信息
        $get_sku_data = WmsProductDetails::getGoodsId($post_data['sku']);
        //历史最低采购单价
//        $lowest_price = PurchasesDetail::getLowestPrice($get_sku_data['id']);
        $new_post_data['purchase_id'] = $post_data['purchase_id'];
        $new_post_data['action_user_id'] = $user_id;
        //采购时间
        date_default_timezone_set("Asia/Shanghai");
        $new_post_data['action_time'] = date('Y-m-d H:i:s');

        foreach ($post_data as $key => $value)
        {
            if ($key == 'sku'){
                $new_post_data['good_id'] = $get_sku_data['id'];
            }elseif ($key == 'status'){
//                $new_post_data['status'] = array_search(trim($value), WmsPurchasesDetail::$status);
                $new_post_data['status'] = $value;
            } else {
                $new_post_data[$key] = $value;
            }
        }
        $result = Yii::$app->db->createCommand()->insert('wms_purchases_detail', $new_post_data)->execute();

        if ($result) {
            return ['code' => 200, 'msg' => '成功'];
        } else {
            return ['code' => 500, 'msg' => '失败'];
        }
    }

    /**
     * @param $id
     * @return array
     * @throws yii\db\Exception
     * 修改采购详情信息
     */
    public function actionUpdate($id)
    {
        //获取表单信息
        $post_data = Yii::$app->request->post();
        //获取当前用户ID
        $user_id = Yii::$app->user->id;
        $post_data['action_user_id'] = $user_id;
        //采购时间
        date_default_timezone_set("Asia/Shanghai");
        $post_data['action_time'] = date('Y-m-d H:i:s');

        $result = Yii::$app->db->createCommand()->update('wms_purchases_detail', $post_data, ['=', 'id', $id])->execute();

        if ($result) {
            return ['code' => 200, 'msg' => '成功'];
        } else {
            return ['code' => 500, 'msg' => '失败'];
        }
    }

    /**
     * @return string
     * 取消采购的订单从采购单数量中减去（仅限状态为待采购、已取消）
     */
    public function actionUpdatePurchasesCount()
    {
        $sku = $_POST['sku'];
        $count = $_POST['count'];
        $id = wmsProductDetails::find()->where(['=','sku_code',$sku])->scalar();
//        $wmsProductDetails = new wmsProductDetails;
//        $data = $wmsProductDetails::getGoodsId($sku);
        $query = WmsPurchasesDetail::find()->where(['good_id'=>$id])->one();
        $query->count = $query->count - $count;
        if ($query->count <= 0){
            $query->count = 0;
        }
        $query = $query->save();
        if ($query){
            $message = '更新成功!';
            return $message;
        } else {
            $message = '更新失败!!';
            return $message;
        }
    }

    /**
     * @throws yii\db\Exception
     * 【确认采购单】
     * ①待采购状态的单手动取消
     * ②缺货状态的单归入缺货单
     * ③更新采购单状态为已采购
     */
    public function actionConfirm()
    {
        $post_data = Yii::$app->request->post();
        $ids = $post_data['ids'];
        $purchase_id = $post_data['purchase_id'];

//        $result = WmsPurchasesDetail::find()->joinWith(['purchases'])->where(['wms_purchases_detail.id' => $ids])->all();
        $result = WmsPurchasesDetail::find()->where(['id' => $ids, 'purchase_id' => $purchase_id])->asArray()->all();

        //①待采购状态的单手动取消
        foreach ($result as $value)
        {
            if (WmsPurchasesDetail::$status[$value['status']] == '待采购'){
                return ['code' => 500, 'msg' => '仍有产品未处理'];
            }
        }

        //②缺货状态的单归入缺货单
//        foreach ($result as $value)
//        {
//            if ($value['status'] == (int)2){
//
//            }
//        }

        //③更新采购单状态
        $purchases = new WmsPurchases();
        //当前采购单状态
        $before_status = $purchases->getStatus($purchase_id);
        if (WmsPurchases::$status[$before_status] == '待采购'){
            $update_purchases_status = $purchases->setPurchasesStatus($purchase_id, '待采购', '已采购');
            if ($update_purchases_status) {
                return ['code' => 200, 'msg' => '成功'];
            } else {
                return ['code' => 500, 'msg' => '采购单状态更新失败'];
            }
        } else {
            return ['code' => 500, 'msg' => '请检查当前采购单状态是否已经更新'];
        }

    }

    /**
     * @throws yii\db\Exception
     * 【提交付款申请】
     * ①更新采购详情单状态：已采购=>待付款
     * ②生成付款单，默认状态为待付款
     * ③更新采购单状态为待付款
     */
    public function actionPaymentRequest()
    {
        $post_data = Yii::$app->request->post();
        $purchases_detail_ids = $post_data['ids'];
        $purchase_id = $post_data['purchase_id'];
        //①更新采购详情单状态：已采购=>待付款
        $purchasesDeatailModel = new WmsPurchasesDetail();
        $update_purchases_detail_status = $purchasesDeatailModel->setPurchasesDetailStatus($purchases_detail_ids,'已采购','待付款');

        if ($update_purchases_detail_status) {
            //②生成付款单，默认状态为待付款
            $results = WmsPurchasesDetail::find()->where(['id' => $purchases_detail_ids, 'status' => array_search('待付款', WmsPurchasesDetail::$status), 'purchase_id' => $purchase_id])->asArray()->all();

            if (!empty($results)){
                $sum = 0;
                $arr = [];
                foreach ($results as $value)
                {
                    //合计（产品单价*产品采购数量）
                    $total = $value['price'] * $value['count'];
                    //总和
//                    $sum += $total;
                    //自定义付款单号
                    $ref = date('YmdHis').rand(100,999);
                    //生成付款单时间
                    date_default_timezone_set("Asia/Shanghai");
                    $create_time = date('Y-m-d H:i:s');
                    //数据存数组
                    $arr[] = [
                        'ref'           => $ref,
//                        'balance_money' => $sum,
                        'pay_money'     => $total,
                        'purchase_id'     => $purchase_id,
                        'purchases_detail_id'   => $value['id'],
                        'status'        => array_search('待付款', WmsPayments::$status),
                        'create_time'   => $create_time,
                    ];
                }
                //批量插入
                if (isset($arr)){
                    $result = Yii::$app->db->createCommand()->batchInsert(WmsPayments::tableName(''),
                        [
                            'ref',
//                            'balance_money',
                            'pay_money',
                            'purchase_id',
                            'purchases_detail_id',
                            'status',
                            'create_time'
                        ], $arr)
                        ->execute();

                    if ($result){
                        //③更新采购单状态为待付款
                        $purchases = new WmsPurchases();
                        //当前采购单状态
                        $before_status = $purchases->getStatus($purchase_id);
                        if (WmsPurchases::$status[$before_status] == '已采购' || WmsPurchases::$status[$before_status] == '付款失败'){
                            $after_status = array_search('待付款', WmsPurchases::$status);
                            $update_purchases_status = $purchases->setPurchasesStatus($purchase_id, $before_status, $after_status);
                            if ($update_purchases_status) {
                                return ['code' => 200, 'msg' => '成功'];
                            } else {
                                return ['code' => 500, 'msg' => '5采购单状态更新失败'];
                            }
                        } else {
                            return ['code' => 500, 'msg' => '4请检查当前采购单状态是否已经更新'];
                        }
                    } else {
                        return ['code' => 500, 'msg' => '3生成付款单失败'];
                    }
                } else {
                    return ['code' => 500, 'msg' => '2生成付款单失败'];
                }
            }
        } else {
            return ['code' => 500, 'msg' => '1采购详情状态更新失败或者已更新'];
        }

    }

}
