<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_sr_bill".
 *
 * @property string $id 暂时TODO
 * @property string $ref 单号
 * @property string $status
 * @property string $action_time 操作时间
 * @property string $action_user_id 操作人
 * @property string $customer_id 顾客id
 * @property string $create_time 生成时间
 * @property string $inventory_money 金额
 * @property string $warehouse_id 仓库id
 * @property string $order_id 对应 订单ID
 */
class WmsSrBill extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_sr_bill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'action_time', 'action_user_id', 'customer_id', 'create_time', 'inventory_money', 'warehouse_id', 'order_id'], 'required'],
            [['status', 'warehouse_id', 'order_id'], 'integer'],
            [['action_time', 'create_time'], 'safe'],
            [['inventory_money'], 'number'],
            [['id', 'ref', 'action_user_id', 'customer_id'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref' => 'Ref',
            'status' => 'Status',
            'action_time' => 'Action Time',
            'action_user_id' => 'Action User ID',
            'customer_id' => 'Customer ID',
            'create_time' => 'Create Time',
            'inventory_money' => 'Inventory Money',
            'warehouse_id' => 'Warehouse ID',
            'order_id' => 'Order ID',
        ];
    }
}
