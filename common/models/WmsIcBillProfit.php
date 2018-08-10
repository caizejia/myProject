<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_ic_bill_profit".
 *
 * @property string $id
 * @property int $ic_bill_id 对应盘点ic_bill 的id
 * @property string $spu
 * @property string $sku
 * @property int $goods_id wms_goods id
 * @property string $create_time
 * @property string $status 盘盈，盘亏
 * @property string $product_name 商品名称
 * @property int $number 账存数量
 * @property int $actual_number 实际数量
 * @property int $profit_number 盘盈数量
 * @property double $profit_cost 盘盈金额
 * @property int $loss_number 盘亏数量
 * @property int $loss_cost 盘亏金额
 * @property string $comment 备注
 * @property int $profit_user 盘盈操作人
 * @property int $loss_user 盘亏操作人
 * @property string $profit_number_time 盘盈修改时间
 * @property string $loss_number_time 盘亏修改时间
 * @property double $total 总额
 */
class WmsIcBillProfit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_ic_bill_profit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ic_bill_id', 'goods_id', 'number', 'actual_number', 'profit_number', 'loss_number', 'loss_cost', 'profit_user', 'loss_user'], 'integer'],
            [['create_time', 'profit_number_time', 'loss_number_time'], 'safe'],
            [['profit_cost', 'total'], 'number'],
            [['comment'], 'string'],
            [['spu', 'sku', 'product_name'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ic_bill_id' => 'Ic Bill ID',
            'spu' => 'Spu',
            'sku' => 'Sku',
            'goods_id' => 'Goods ID',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'product_name' => 'Product Name',
            'number' => 'Number',
            'actual_number' => 'Actual Number',
            'profit_number' => 'Profit Number',
            'profit_cost' => 'Profit Cost',
            'loss_number' => 'Loss Number',
            'loss_cost' => 'Loss Cost',
            'comment' => 'Comment',
            'profit_user' => 'Profit User',
            'loss_user' => 'Loss User',
            'profit_number_time' => 'Profit Number Time',
            'loss_number_time' => 'Loss Number Time',
            'total' => 'Total',
        ];
    }
}
