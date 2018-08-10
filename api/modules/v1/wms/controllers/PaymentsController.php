<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5
 * Time: 18:42
 */

namespace api\modules\v1\wms\controllers;

use common\models\WmsPurchasesDetail;
use common\models\WmsPwBill;
use yii;
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsPayments;
use common\models\WmsPaymentsSearch;
use common\models\Adminuser;
use common\models\WmsProductDetails;
use common\models\WmsPurchases;


class PaymentsController extends CommonController
{
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
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    public function actionIndex()
    {
        $searchModel = new WmsPaymentsSearch;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (empty($dataProvider->getModels())){
            return false;
        }

        $sum = 0;
        foreach ($dataProvider->getModels() as $value)
        {
//            foreach ($value['purchasesDetail'] as $val)
//            {
            //付款人姓名
            $username = empty($value['pay_user_id'])?'空':Adminuser::getUsername($value['pay_user_id']);
            //通过公共方法获取sku
            $cacheData = WmsProductDetails::getFullInfo($value['purchasesDetail']['good_id']);
            //合计（产品单价*产品采购数量）
            $total = $value['purchasesDetail']['price'] * $value['purchasesDetail']['count'];
            //总和
            $sum += $total;
            //返回数据（暂时缺运费、币种）
            $data[] = [
                'id' => $value['id'],  //付款单ID
                'ref' => $value['ref'],  //付款单号
                'sku' => empty($cacheData['sku_code'])?'':$cacheData['sku_code'],
                'purchase_ref' => $value['purchases']['ref'],  //采购单号
                'supplier_platform' => $value['purchases']['supplier_platform'],  //平台
                'supplier_ref' => $value['purchasesDetail']['supplier_ref'],  //平台单号
                'price' => $value['purchasesDetail']['price'],  //单价金额
                'count' => $value['purchasesDetail']['count'],  //产品采购数量
                'create_time' => $value['create_time'],  //付款单生成时间
                'action_time' => $value['action_time'],  //付款时间
                'username' => $username,  //付款人
                'status' => WmsPayments::$status[$value['status']],  //状态
                'total' => $total
            ];
//            }

        }

//        $data['_sum'] = $sum;

        $data = $this->wrapData($data,$dataProvider->pagination);

        return $data;
    }

    /**
     * @param $id
     * @return array|void
     * @throws \Yii\db\Exception
     * 【付款】【问题单】
     */
    public function actionUpdate($id)
    {
        $model = new WmsPayments();
        //获取表单信息
        $post_data = Yii::$app->request->post();
        switch ($post_data['number'])
        {
            case -1:
                $status = '已付款';
                $result = $model->payment($id, $status);
                break;

            case -2:
                $status = '取消单';
                $result = $model->problem($id, $status);
                break;

            default:
                $result = false;

        }
        if ($result) {
            return $result;
        }

    }
}
