<?php

namespace common\models;

use api\models\Supplier;
use Yii;

/**
 * This is the model class for table "wms_purchases".
 *
 * @property integer $id
 * @property string $ref
 * @property integer $supplier_id
 * @property string $supplier_name
 * @property string $supplier_platform
 * @property string $supplier_ref
 * @property integer $action_user_id
 * @property string $action_time
 * @property string $create_time
 * @property integer $status
 * @property string $finish_time
 * @property string $money
 * @property integer $confirm_user_id
 * @property string $confirm_time
 * @property integer $pw_bill_id
 * @property string $memo
 */
class WmsPurchases extends \yii\db\ActiveRecord
{
    public static $status = [
        0 => '待采购',
        1 => '已采购',
        2 => '缺货',
        3 => '取消单',
        4 => '待付款',
        5 => '已付款',
        6 => '付款失败',
        7 => '收货中',
        8 => '退款中',
        9 => '退货中',
        10 => '已完成',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_purchases';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref', 'supplier_id', 'supplier_name', 'supplier_platform', 'supplier_ref', 'action_user_id', 'action_time', 'status', 'finish_time', 'money', 'confirm_user_id', 'confirm_time', 'pw_bill_id'], 'required'],
            [['supplier_id', 'action_user_id', 'status', 'confirm_user_id', 'pw_bill_id'], 'integer'],
            [['action_time', 'create_time', 'finish_time', 'confirm_time'], 'safe'],
            [['money'], 'number'],
            [['ref', 'memo'], 'string', 'max' => 255],
            [['supplier_name'], 'string', 'max' => 250],
            [['supplier_platform', 'supplier_ref'], 'string', 'max' => 50],
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
            'supplier_id' => 'Supplier ID',
            'supplier_name' => 'Supplier Name',
            'supplier_platform' => 'Supplier Platform',
            'supplier_ref' => 'Supplier Ref',
            'action_user_id' => 'Action User ID',
            'action_time' => 'Action Time',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'finish_time' => 'Finish Time',
            'money' => 'Money',
            'confirm_user_id' => 'Confirm User ID',
            'confirm_time' => 'Confirm Time',
            'pw_bill_id' => 'Pw Bill ID',
            'memo' => 'Memo',
        ];
    }

    /**
     * 生成采购单
     * @param $order
     * @return bool
     * @throws \yii\db\Exception
     * 流程：检查订单内详细商品*数量， 检查有没有足够库存，锁定必要库存，生成采购单 
     */
    public function purchaseOrder($order){
        $items = Yii::$app->db->createCommand("SELECT * FROM oms_order_goods WHERE oid = '{$order['order_id']}' ORDER BY sku_code ASC")->queryAll();
        $wms_inventory = new WmsInventory1();
        $wms_product_details = new WmsProductDetails();
        $res_cg = [];
        $cgd = [];
        foreach ($items as $item){
            $res = $wms_inventory->checkInventory($item['number'],$item['sku_code']);
            $goods_id = ($wms_product_details::getGoodsId($item['sku_code']))->id;
            if(!$res){
                $number_keyong = $wms_inventory->inventoryBySku($item['sku_code']);
                Yii::$app->db->createCommand("UPDATE wms_inventory SET lock_count = lock_count + '{$number_keyong}' WHERE goods_id = '{$goods_id}'")->execute();
                $number_caigou = $item['number'] - $number_keyong;

                //订单应该带来供应商信息 supplier_id
                $supplier = Yii::$app->db->createCommand("SELECT * FROM oms_purchase_link WHERE sku_code = '{$item['sku_code']}' limit 1")->queryOne();


                if(isset($cgd[$supplier['id']][$item['sku_code']])){
                    $cgd[$supplier['id']][$item['sku_code']]['sku'] += $item['number'];
                }else{
                    $cgd[$supplier['id']][$item['sku_code']]['sku'] = $item['number'];
                    $cgd[$supplier['id']][$item['sku_code']]['price'] = $supplier['price'];
                }

                $res_cg[] = true;
            }else{
                Yii::$app->db->createCommand("UPDATE wms_inventory SET lock_count = lock_count + '{$item['number']}' WHERE goods_id = '{$goods_id}'")->execute();
                $res_cg[] = false;
            }
        }

        if(in_array(true,$res_cg)){
            $status = 3;
            $remark = date('Y-m-d H:i:s') . '生成采购单时匹配需采购改为待采购';
        }else{
            $status = 5;
            $remark = date('Y-m-d H:i:s') . '生成采购单时匹配到库存无需采购改为待发货';
        }

        //写采购单
        foreach ($cgd as $k_cgd=>$v_cgd){

            // TODO 判断今天是否已经有此供应商的采购，if(！$exist)， 把采购详情归到此采购单
            Yii::$app->db->createCommand()->insert("wms_purchases",[
                        'ref' => 'Pc'.date('YmdHis'),
                        'supplier_id' => $k_cgd,
                        'supplier_name' => $supplier['supplier_name'],
                        'supplier_platform' => $supplier['supplier_platform'] 
                    ])->execute();


            //OMS的订单 结构和字段 不确定，所以暂时停下 TODO
            foreach ($v_cgd as $k=>$v){
                Yii::$app->db->createCommand()->insert("wms_purchases_detail",[
                    'good_id' => '',
                    'supplier_ref' => '',
                    'link' => '',
                    'should_count' => '',
                    'count' => '',
                    'price' => '',
                    'total_price' => '',
                    'logistics' => '',
                    'purchase_id' => '',
                    'add_library_count' => '',
                    'minus_library_count' => '',
                    'status' => '',
                    'desc' => '',
                    'memo' => '',
                    'action_user_id' => '',
                    'action_time' => '',
                    'finish_time' => '',
                    'confirm_user_id' => '',
                    'confirm_time' => '',
                    'pw_bill_id' => '',
                    'supplier_id' => '',
                ])->execute();
            }
        }
 
        //设置订单状态
        $order->setOrderStatus($order['order_id'],$order['status'],$status,$remark, Yii::$app->user->id);
    }

    /**
     * @return
     * 连表查 and wms_purchases_detail
     */
    public function getPurchasesDetail()
    {
        return $this->hasMany(WmsPurchasesDetail::className(), ['purchase_id' => 'id']);
    }

    /**
     * @return
     * 连表查 and oms_supplier
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['purchase_id' => 'id']);
    }

    /**
     * @param $purchases_detail_id
     * @param $before_status
     * @param $after_status
     * @throws \yii\db\Exception
     * 采购单状态改变
     */
    public function setPurchasesStatus($purchases_id,$before,$after)
    {
        $before_status = is_numeric($before)?$before:array_search($before, WmsPurchases::$status);
        $after_status = is_numeric($after)?$after:array_search($after, WmsPurchases::$status);
        $result = Yii::$app->db->createCommand()->update('wms_purchases', [
            'status' => $after_status,
        ], ['id' => $purchases_id, 'status' => $before_status])->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return false|null|string
     * 获取当前采购单状态
     */
    public function getStatus($id)
    {
        $purchases = new WmsPurchases();
        $status = $purchases->find()->where(['=', 'id', $id])->select('status')->asArray()->scalar();
        return $status;
    }
}
