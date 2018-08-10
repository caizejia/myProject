<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_pr_bill".
 *
 * @property string $id
 * @property integer $goods_id
 * @property string $rejection_goods_count
 * @property string $rejection_goods_price
 * @property string $rejection_money
 * @property integer $status
 * @property string $action_time
 * @property integer $action_user_id
 * @property integer $supplier_id
 * @property string $create_time
 * @property integer $warehouse_id
 * @property integer $purchases_detail_id
 * @property string $pw_bill_detail_id
 */
class WmsPrBill extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public static $status = [
        0 => '待确认',
        1 => '待出库',
        2 => '已出库',
        3 => '已退货',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_pr_bill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'rejection_goods_count', 'rejection_goods_price', 'rejection_money'], 'required'],
            [['id', 'goods_id', 'status', 'action_user_id', 'supplier_id', 'warehouse_id', 'purchases_detail_id', 'pw_bill_detail_id'], 'integer'],
            [['rejection_goods_count', 'rejection_goods_price', 'rejection_money'], 'number'],
            [['action_time', 'create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品id',
            'rejection_goods_count' => '退货数量',
            'rejection_goods_price' => '退货单价',
            'rejection_money' => '退货金额',
            'status' => '0:待出库 1：已出库			',
            'action_time' => '操作时间',
            'action_user_id' => '操作人员',
            'supplier_id' => '供应商id',
            'create_time' => '采购退货生成时间',
            'warehouse_id' => '仓库id',
            'purchases_detail_id' => '采购采购单id',
            'pw_bill_detail_id' => '对应采购入库单（收货单)商品明细id',
        ];
    }

}
