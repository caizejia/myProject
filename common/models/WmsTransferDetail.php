<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_transfer_detail".
 *
 * @property string $id
 * @property string $sku
 * @property string $goods_count 调拨数量
 * @property string $outqty 出库数量
 * @property string $inqty 入库数量
 * @property string $goods_money 出库金额
 * @property string $goods_price 销售单价
 * @property string $ws_bill_id 主表id
 * @property string $memo 备注
 */
class WmsTransferDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_transfer_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_count', 'outqty', 'inqty', 'ws_bill_id'], 'integer'],
            [['goods_money', 'goods_price'], 'number'],
            [['sku'], 'string', 'max' => 50],
            [['memo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'Sku',
            'goods_count' => 'Goods Count',
            'outqty' => 'Outqty',
            'inqty' => 'Inqty',
            'goods_money' => 'Goods Money',
            'goods_price' => 'Goods Price',
            'ws_bill_id' => 'Ws Bill ID',
            'memo' => 'Memo',
        ];
    }
}
