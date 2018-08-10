<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_order_time".
 *
 * @property string $id 订单时间表id
 * @property string $oid 订单id
 * @property string $order_time 下单时间
 * @property string $confirm_time 确认时间
 * @property string $purchase_time 采购时间
 * @property string $on_ship_time 待发货时间
 * @property string $picking_time 拣货时间
 * @property string $pack_time 打包时间
 * @property string $stock_out_time 出库时间
 * @property string $ship_pickup_time 货代收货时间
 * @property string $pickup_time 上线时间
 * @property string $delivery_time 签收时间
 * @property string $denial_of_time 拒收时间
 * @property string $shipping_time 配送时间
 * @property string $remove_time 取消时间
 * @property string $back_time 回款时间
 * @property string $final_time 结算时间
 * @property string $create_time_end 结束时间
 * @property int $is_del 是否删除（0否 1是）
 */
class OrderTime extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_order_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid'], 'integer'],
            [['order_time', 'confirm_time', 'purchase_time', 'on_ship_time', 'picking_time', 'pack_time', 'stock_out_time', 'ship_pickup_time', 'pickup_time', 'delivery_time', 'denial_of_time', 'shipping_time', 'remove_time', 'back_time', 'final_time', 'create_time_end'], 'safe'],
            [['is_del'], 'string', 'max' => 1],
            [['oid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'oid' => 'Oid',
            'order_time' => 'Order Time',
            'confirm_time' => 'Confirm Time',
            'purchase_time' => 'Purchase Time',
            'on_ship_time' => 'On Ship Time',
            'picking_time' => 'Picking Time',
            'pack_time' => 'Pack Time',
            'stock_out_time' => 'Stock Out Time',
            'ship_pickup_time' => 'Ship Pickup Time',
            'pickup_time' => 'Pickup Time',
            'delivery_time' => 'Delivery Time',
            'denial_of_time' => 'Denial Of Time',
            'shipping_time' => 'Shipping Time',
            'remove_time' => 'Remove Time',
            'back_time' => 'Back Time',
            'final_time' => 'Final Time',
            'create_time_end' => 'Create Time End',
            'is_del' => 'Is Del',
        ];
    }
}
