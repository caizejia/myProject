<?php

namespace common\models;

use Yii;

use common\models\WmsPurchasesDetail;
use common\models\WmsProductDetails;

/**
 * This is the model class for table "wms_pw_bill".
 *
 * @property string $ref 收货单号
 * @property string $goods_id 商品id
 * @property string $goods_count 商品收货数量
 * @property string $goods_money 商品采购金额
 * @property string $goods_price 商品采购单价
 * @property int $status 0:待入库 1：已入库 2: 已退货
 * @property string $action_time 业务日期
 * @property string $action_user_id 业务人员id
 * @property string $create_time 单据生成时间
 * @property string $supplier_id 供应商id
 * @property string $warehouse_id 仓库id
 * @property string $memo 备注
 */
class WmsPwBill extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public static $status = [
//        0 => '待采购',
//        1 => '已采购',
//        2 => '缺货',
//        3 => '待付款',
//        4 => '已付款',
//        5 => '付款失败',
        0 => '收货中',
        1 => '退货中',
        2 => '已完成',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_pw_bill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref', 'goods_id', 'goods_count', 'goods_money', 'goods_price', 'status', 'action_time', 'action_user_id', 'create_time', 'supplier_id'], 'required'],
            [['goods_id', 'goods_count', 'status', 'action_user_id', 'supplier_id', 'warehouse_id'], 'integer'],
            [['goods_money', 'goods_price'], 'number'],
            [['action_time', 'create_time'], 'safe'],
            [['ref', 'memo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref' => '单号',
            'goods_id' => '商品id',
            'goods_count' => '商品采购数量',
            'goods_money' => '商品采购金额',
            'goods_price' => '商品采购单价',
            'status' => '0:待入库 1：已入库 2: 已退货',
            'action_time' => '业务日期',
            'action_user_id' => '业务人员id',
            'create_time' => '单据生成时间',
            'supplier_id' => '供应商id',
            'warehouse_id' => '仓库id',
            'memo' => '备注',
        ];
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
     * @return
     * 连表wms_product_details
     */
    public function getProductDetails()
    {
        return $this->hasOne(WmsProductDetails::className(),['id' => 'goods_id']);
    }

    /**
     * Creates a new PwBill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * 生成收货单
     */
    public function createPwBill($purchase_id)
    {
        date_default_timezone_set("Asia/Shanghai");
//        $post_data = Yii::$app->request->post();

        if($purchase_id > 0){
            $purchases_details = WmsPurchasesDetail::find()->where(['purchase_id' => $purchase_id, 'status' => array_search('已付款', WmsPurchasesDetail::$status)])->with(['purchases'])->asArray()->all();
//            print_r($purchases_details);die;
            if (count($purchases_details) > 0){
                foreach ($purchases_details as $value)
                {
                    $warehouse_id = 1;
                    $goods_id = $value['good_id'];
                    $count = $value['count'];
                    //自定义收货单号
                    $ref = date('YmdHis').rand(10,99);
                    //收货单生成时间
                    $create_time = date('Y-m-d H:i:s');

                    $result = Yii::$app->db->createCommand()->insert("wms_pw_bill", [
                        "ref"                 => $ref,
                        "goods_id"            => $goods_id,
                        "goods_money"         => $value['count'] * $value['price'],
                        "goods_price"         => $value['price'],
                        "status"              => array_search('收货中', WmsPwBill::$status),
                        "create_time"         => $create_time,
//                        "supplier_id"         => $value['purchases']['supplier_id'],
                        "supplier_id"         => 1,
                        "warehouse_id"        => $warehouse_id,
                        "purchases_detail_id" => $value['id'],
                    ])->execute();

                    if ($result){
                        //增加在途
                        $inventory = new WmsInventory1();
                        $inventory->inAfloat($warehouse_id, $goods_id, $count);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $ids
     * @return array
     * @throws \yii\db\Exception
     * 生成退货单
     */
    public function createReturnedPurchase($ids)
    {
        //①生成退货单
        date_default_timezone_set("Asia/Shanghai");
        $data = self::find()->where(['id' => $ids])->asArray()->all();

        if (count($data) > 0) {
            $new_data = [];
            foreach ($data as $value)
            {
                if (self::$status[$value['status']] == '已完成') {
                    //更新收货单状态
                    $before_status = $value['status'];
                    $after_status = array_search('退货中', self::$status);
                    $res = $this->setPwBillStatus($value['id'],$before_status,$after_status);
                    if (!isset($res)){
                        //可记录日志
//                        $message = $value['id'].'更新收货单状态失败';
                        continue;
                    }
                    //退货单生成时间
                    $create_time = date('Y-m-d H:i:s');
                    $new_data[] = [
                        "goods_id"               => $value['goods_id'],
                        "rejection_goods_count"  => $value['goods_count'],
                        "rejection_goods_price"  => $value['goods_price'],
                        "rejection_money"        => $value['goods_count'] * $value['goods_price'],
                        "status"                 => array_search('待确认', WmsPrBill::$status),
                        "supplier_id"            => $value['supplier_id'],
                        "create_time"            => $create_time,
                        "warehouse_id"           => $value['warehouse_id'],
                        "purchases_detail_id"    => $value['purchases_detail_id'],
                        "pw_bill_detail_id"      => $value['id'],
                    ];
                }
            }

            //批量插入
            if (count($new_data) > 0) {
                $result = Yii::$app->db->createCommand()->batchInsert(WmsPrBill::tableName(''),
                    [
                        'goods_id',
                        'rejection_goods_count',
                        'rejection_goods_price',
                        'rejection_money',
                        'status',
                        'supplier_id',
                        'create_time',
                        'warehouse_id',
                        'purchases_detail_id',
                        'pw_bill_detail_id',
                    ], $new_data)
                    ->execute();

                if ($result){
                    return ['code' => 200, 'msg' => '成功'];
                } else {
                    return ['code' => 500, 'msg' => '生成退货单失败'];
                }
            } else {
                return ['code' => 500, 'msg' => '生成退货单失败'];
            }
        } else {
            return ['code' => 500, 'msg' => '未选中内容'];
        }
    }

    /**
     * @param $purchases_id
     * @param $before_status
     * @param $after_status
     * @return bool
     * @throws \yii\db\Exception
     * 收货单状态改变
     */
    public function setPwBillStatus($id,$before_status,$after_status)
    {
        $result = Yii::$app->db->createCommand()->update('wms_pw_bill', [
            'status' => $after_status,
        ], ['id' => $id, 'status' => $before_status])->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
