<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_payments".
 *
 * @property string $id
 * @property string $ref
 * @property string $act_money
 * @property string $balance_money
 * @property string $pay_money
 * @property integer $purchase_id
 * @property integer $pay_user_id
 * @property integer $status
 * @property integer $ca_type
 * @property string $create_time
 * @property string $action_time
 */
class WmsPayments extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public static $status = [
        0 => '待付款',
        1 => '已付款',
        2 => '付款失败',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref', 'act_money', 'balance_money', 'pay_money', 'purchases_detail_id', 'status', 'ca_type', 'create_time'], 'required'],
            [['act_money', 'balance_money', 'pay_money'], 'number'],
            [['purchase_id', 'pay_user_id', 'status', 'ca_type'], 'integer'],
            [['ref', 'create_time', 'action_time'], 'safe'],
            [['ref'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref' => 'Ref',
            'act_money' => 'Act Money',
            'balance_money' => 'Balance Money',
            'pay_money' => 'Pay Money',
            'purchases_detail_id' => 'Purchase Detail ID',
            'pay_user_id' => 'Pay User ID',
            'status' => 'Status',
            'ca_type' => 'Ca Type',
            'create_time' => 'Create Time',
            'action_time' => 'Action Time',
        ];
    }

    /**
     * @return
     * 连表查 and wms_purchases
     */
    public function getPurchases()
    {
        return $this->hasOne(WmsPurchases::className(), ['id' => 'purchase_id']);
    }

    /**
     * @return
     * 连表查 and wms_purchases_detail
     */
    public function getPurchasesDetail()
    {
        return $this->hasOne(WmsPurchasesDetail::className(), ['id' => 'purchases_detail_id']);
    }

    /**
     * @param $purchases_id
     * @param $before_status
     * @param $after_status
     * @return int
     * @throws \yii\db\Exception
     * 付款单状态改变
     */
    public function setPaymentsStatus($payments_id,$before_status,$after_status)
    {
        $result = Yii::$app->db->createCommand()->update('wms_payments', [
            'status' => $after_status,
        ], ['id' => $payments_id, 'status' => $before_status])->execute();

        return $result;
    }

    /**
     * @param $id
     * @return array
     * @throws yii\db\Exception
     * 点击付款更新相关信息及状态
     * ①更新付款单信息
     * ②更新采购详情状态
     * ③更新采购单状态
     * ④生成收货单
     * ⑤改变采购详情和采购单的状态为：收货中
     */
    public function payment($id)
    {
        //获取当前用户ID
        $user_id = Yii::$app->user->id;
        $post_data['pay_user_id'] = $user_id;
        //付款时间
        date_default_timezone_set("Asia/Shanghai");
        $post_data['action_time'] = date('Y-m-d H:i:s');
        //当前付款单状态
        $result = self::find()->where(['=', 'id', $id])->select('purchase_id, purchases_detail_id, status')->asArray()->one();

        if (self::$status[$result['status']] == '待付款'){
            $post_data['status'] = array_search('已付款', self::$status);
        } else {
            return ['code' => 500, 'msg' => '请查看该付款单是否已经付款或取消'];
        }

        //①更新付款单信息
        $update_payments = Yii::$app->db->createCommand()->update('wms_payments', $post_data, ['=', 'id', $id])->execute();

        if ($update_payments){
            //②更新采购详情状态
            $purchasesDeatailModel = new WmsPurchasesDetail();
            $purchases_detail_id = $result['purchases_detail_id'];
            $update_purchases_detail_status = $purchasesDeatailModel->setPurchasesDetailStatus($purchases_detail_id,'待付款','已付款');
            if (!$update_purchases_detail_status){
                return ['code' => 500, 'msg' => '2采购详情状态更新失败或者已更新'];
            }
            //当前采购单中的待付款单数
            $purchase_id = $result['purchase_id'];
            $count = WmsPurchasesDetail::find()->where(['purchase_id' => $purchase_id, 'status' => array_search('待付款', WmsPurchasesDetail::$status)])->count();

            switch ($count)
            {
                case 0:
                    //③更新采购单状态
                    $purchasesModel = new WmsPurchases();
                    //当前采购单状态
                    $before_status = $purchasesModel->getStatus($purchase_id);
                    if (WmsPurchases::$status[$before_status] == '待付款'){
                        $update_purchases_status = $purchasesModel->setPurchasesStatus($purchase_id, '待付款', '已付款');
                        if (!$update_purchases_status) {
                            return ['code' => 500, 'msg' => '4采购单状态更新失败'];
                        }

                        //④生成收货单
                        $pwbill = new WmsPwBill();
                        $ret = $pwbill->createPwBill($purchase_id);
                        if (!$ret){
                            return ['code' => 500, 'msg' => '5收货单生成失败'];
                        }

                        //⑤改变采购详情和采购单的状态为：收货中
                        $update_purchases_detail_status = $purchasesDeatailModel->setPurchasesDetailStatus($purchases_detail_id,'已付款','收货中');
                        if (!$update_purchases_detail_status) {
                            return ['code' => 500, 'msg' => '6采购详情状态更新失败或者已更新'];
                        }
                        //当前采购单中的已付款单数
                        $count = WmsPurchasesDetail::find()->where(['purchase_id' => $result['purchase_id'], 'status' => array_search('已付款', WmsPurchasesDetail::$status)])->count();
                        if ($count == 0) {
                            $purchasesModel = new WmsPurchases();
                            //当前采购单状态
                            $before_status = $purchasesModel->getStatus($result['purchase_id']);
                            if (WmsPurchases::$status[$before_status] == '已付款') {
                                $update_purchases_status = $purchasesModel->setPurchasesStatus($result['purchase_id'], '已付款', '收货中');
                                if ($update_purchases_status) {
                                    return ['code' => 200, 'msg' => '付款成功'];
                                } else {
                                    return ['code' => 500, 'msg' => '8采购单状态更新失败'];
                                }
                            } else {
                                return ['code' => 500, 'msg' => '7请检查当前采购单状态是否已经更新'];
                            }
                        }
                    } else {
                        return ['code' => 500, 'msg' => '3请检查当前采购单状态是否已经更新'];
                    }
                    break;

                default:
                    //④生成收货单
                    $pwbill = new WmsPwBill();
                    $ret = $pwbill->createPwBill($purchase_id);
                    if ($ret){
                        return ['code' => 200, 'msg' => '付款成功'];
                    } else {
                        return ['code' => 500, 'msg' => '5收货单生成失败'];
                    }
            }
        } else {
            return ['code' => 500, 'msg' => '1付款单信息更新失败'];
        }

    }

    /**
     * @param $id
     * @param $_status
     * @return array
     * @throws \yii\db\Exception
     * 点击问题单更新相关信息及状态
     */
    public function problem($id, $_status)
    {
        //获取当前用户ID
        $user_id = Yii::$app->user->id;
        $post_data['pay_user_id'] = $user_id;
        //问题单时间
        date_default_timezone_set("Asia/Shanghai");
        $post_data['action_time'] = date('Y-m-d H:i:s');
        //当前付款单状态
        $result = self::find()->where(['=', 'id', $id])->select('purchase_id, purchases_detail_id, status')->asArray()->one();

        if (self::$status[$result['status']] == '待付款'){
            $post_data['status'] = array_search($_status, self::$status);
        } else {
            return ['code' => 500, 'msg' => '请查看该付款单是否已经付款或取消'];
        }

        //①更新付款单信息
        $update_payments = Yii::$app->db->createCommand()->update('wms_payments', $post_data, ['=', 'id', $id])->execute();
        if ($update_payments) {
            //②更新采购详情状态
            $purchasesDeatailModel = new WmsPurchasesDetail();
            $purchases_detail_id = $result['purchases_detail_id'];
            $update_purchases_detail_status = $purchasesDeatailModel->setPurchasesDetailStatus($purchases_detail_id,'待付款','已付款');
            //当前采购单中的待付款单数
            $purchase_id = $result['purchase_id'];
            $count = WmsPurchasesDetail::find()->where(['purchase_id' => $purchase_id, 'status' => array_search('待付款', WmsPurchasesDetail::$status)])->count();

            if ($update_purchases_detail_status){
                if ($count == 0){
                    //③更新采购单状态
                    $purchases = new WmsPurchases();
                    //当前采购单状态
                    $before_status = $purchases->getStatus($result['purchase_id']);
                    if (WmsPurchases::$status[$before_status] == '待付款'){
                        $update_purchases_status = $purchases->setPurchasesStatus($result['purchase_id'], '待付款', '付款失败');
                        if ($update_purchases_status) {
                            return ['code' => 200, 'msg' => '取消成功'];
                        } else {
                            return ['code' => 500, 'msg' => '5采购单状态更新失败'];
                        }
                    } else {
                        return ['code' => 500, 'msg' => '4请检查当前采购单状态是否已经更新'];
                    }
                } else {
                    return ['code' => 500, 'msg' => '3仍有订单未处理'];
                }
            } else {
                return ['code' => 500, 'msg' => '2采购详情状态更新失败或者已更新'];
            }
        } else {
            return ['code' => 500, 'msg' => '1付款单信息更新失败'];
        }

    }

}
