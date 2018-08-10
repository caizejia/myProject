<?php

namespace api\modules\v1\wms\controllers;

use common\models\WmsInventory1;
use common\models\WmsPurchases;
use Yii;
use common\models\WmsPwBill;
use common\models\WmsPwBillSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;
use common\models\Adminuser;
use common\models\WmsProductDetails;
use common\models\WmsPurchasesDetail;

/**
 * PwBillController implements the CRUD actions for PwBill model.
 */
class PwBillController extends CommonController
{
    public $modelClass = 'common\models\WmsPwBill';
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
//        ];
//    }

    /**
     * @return array
     * @重写设置
     */
    public function actions()
    {
        $actions = parent::actions();
        // 禁用"delete" 和 "create" 动作
//        unset($actions['delete']);
//        unset( $actions['create']);
        unset($actions['update']);
        //unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    /**
     * Lists all PwBill models.
     * @return mixed
     * 收货单列表
     */
    /*public function actionIndex()
    {
        $searchModel = new WmsPwBillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        print_r($dataProvider->getModels());die;

        if (empty($dataProvider->getModels())){
            return false;
        }
        $sum = 0;
        foreach ($dataProvider->getModels() as $value)
        {
//          //当前操作人姓名
            $username = empty($value['action_user_id'])?'系统':Adminuser::getUsername($value['action_user_id']);
            //通过公共方法获取sku
            $cacheData = WmsProductDetails::getFullInfo($value['goods_id']);
            //合计（产品单价*产品采购数量）
//            $total = $value['price'] * $value['count'];
            //总和
//            $sum += $total;
            //目前缺的数据：运费、币种、预计到货时间、3天销量、7天销量
            $data[] = [
                'id' => $value['id'],
                'goods_id' => $value['goods_id'],
                'warehouse_id' => $value['warehouse_id'],
                'ref' => $value['ref'],  //收货单号
                'purchases_detail_id' => $value['purchases_detail_id'],  //采购详情单号
                'supplier_platform' => $value['purchasesDetail']['purchases']['supplier_platform'],  //平台
                'supplier_ref' => $value['purchasesDetail']['supplier_ref'],  //平台单号
                'supplier_name' => $value['purchasesDetail']['purchases']['supplier_name'],  //平台单号
                'create_time' => $value['create_time'],  //收货单生成时间
                'action_time' => $value['action_time'],  //收货单处理时间
                'username' => $username,  //操作人
                'status' => WmsPwBill::$status[$value['status']],  //状态
                'sku' => empty($cacheData['sku_code'])?'':$cacheData['sku_code'],  //sku
                'size' => empty($cacheData['sku_attribute'])?'':$cacheData['sku_attribute'],  //属性：大小、尺寸
                'color' => empty($cacheData['sku_attribute'])?'':$cacheData['sku_attribute'],  //属性：颜色
//                'price' => $value['price'],  //当前产品单价***
//                'should_count' => $value['should_count'],  //应采购的数量
                'count' => $value['purchasesDetail']['count'],  //实际采购数量***
                'goods_count' => $value['goods_count'],  //实际采购数量***
//                'total' => $total,
                'memo' => $value['memo']
            ];
        }

//        $data['_sum'] = $sum;

        $data = $this->wrapData($data,$dataProvider->pagination);

        return $data;
    }*/

    /**
     * Displays a single PwBill model.
     * @param string $id
     * @return mixed
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new PwBill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * 新增收货单
     */
   /* public function actionCreate()
    {

    }*/

    /**
     * Updates an existing PwBill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * 收货时更新收货信息
     */
    public function actionUpdate($id)
    {

        //获取表单信息
        $post_data = Yii::$app->request->bodyParams;
        $message = WmsPwBill::find()->where(['id'=>$id])->one();
//        return $message;die;
        $message->ref= $post_data['supplier_ref'];
        $message->memo=$post_data['memo'];
        $message->goods_count=$post_data['count'];
        $message->goods_money=$post_data['prices'];
        $message->goods_price=$post_data['price'];
        $message->status=$post_data['status'];
        //$flag = $message->load(Yii::$app->request->bodyParams, '') && $message->validate();

        $message->save(false);
//        return $message;





//        return $post_data;
//        $goods_id = $post_data['goods_id'];
//        $warehouse_id = $post_data['warehouse_id'];
//        $goods_count = $post_data['goods_count'];
        //获取当前用户ID
        $user_id = Yii::$app->user->id;
        $post_data['action_user_id'] = $user_id;
        //收货时间
        date_default_timezone_set("Asia/Shanghai");
        $post_data['action_time'] = date('Y-m-d H:i:s');
        //确认更新后，状态变为已完成
        if (empty($goods_count)){
            return ['code' => 500, 'msg' => '请填写实收数量'];
        }
        $post_data['status'] = array_search('已完成', WmsPwBill::$status);

        //当前收货单状态
        $pw_bill_status = WmsPwBill::find()->select('status')->where(['=','id',$id])->scalar();
        if (WmsPwBill::$status[$pw_bill_status] != '收货中'){
            return ['code' => 500, 'msg' => '该订单已收货'];
        }
        $result = Yii::$app->db->createCommand()->update('wms_pw_bill', $post_data, ['=', 'id', $id])->execute();
        if ($result){
            //更新详情单状态
            $purchasesDeatailModel = new WmsPurchasesDetail();
            $res = $purchasesDeatailModel->setPurchasesDetailStatus($post_data['purchases_detail_id'],'收货中','已完成');
            if (!$res){
                return ['code' => 500, 'msg' => '采购详情状态更新失败或已更新'];
            }
            //入库
            if ($warehouse_id > 0 && $goods_id > 0){
                $inventory = new WmsInventory1();
                $inventory->inItem($warehouse_id, $goods_id, $goods_count);
            }
            return ['code' => 200, 'msg' => '成功'];
        } else {
            return ['code' => 500, 'msg' => '收货失败'];
        }

    }

    /**
     * Deletes an existing PwBill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
/*    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the PwBill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PwBill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PwBill::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     * 提交退货申请
     */
    /*public function actionReturnedPurchase()
    {
        $model = new WmsPwBill();
        //获取表单信息
        $post_data = Yii::$app->request->post();

        foreach ($post_data as $value)
        {
            $ids[] = $value['id'];
            if (empty($value['count'])){
                return ['code' => 500, 'msg' => $value['id'].'号收货单实收数量不能为空'];
            }
        }

        $result = $model->createReturnedPurchase($ids);
        if ($result){
            return $result;
        }

    }*/
}
