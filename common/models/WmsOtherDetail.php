<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_other_detail".
 *
 * @property string $id
 * @property string $sku 单据编号
 * @property int $outqty 出入库数量
 * @property string $goods_money 出入库金额
 * @property string $goods_price 出入库价格
 * @property int $ws_bill_id 主表id
 * @property string $memo 备注
 */
class WmsOtherDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_other_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['outqty', 'ws_bill_id'], 'integer'],
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
            'outqty' => 'Outqty',
            'goods_money' => 'Goods Money',
            'goods_price' => 'Goods Price',
            'ws_bill_id' => 'Ws Bill ID',
            'memo' => 'Memo',
        ];
    }
}
