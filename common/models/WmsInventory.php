<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_inventory".
 *
 * @property string $id
 * @property string $goods_id 产品id，对应 oms_product_sku的id
 * @property string $balance_count 商品余额数量（包含锁定的）
 * @property string $afloat_count 剩余
 * @property string $lock_count 在途
 * @property string $warehouse_id 锁定
 * @property string $hz_id 货主编号
 */
class WmsInventory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balance_count', 'afloat_count', 'lock_count', 'warehouse_id', 'hz_id'], 'required'],
            [['balance_count', 'afloat_count', 'lock_count', 'warehouse_id', 'hz_id'], 'integer'],
            [['goods_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'balance_count' => 'Balance Count',
            'afloat_count' => 'Afloat Count',
            'lock_count' => 'Lock Count',
            'warehouse_id' => 'Warehouse ID',
            'hz_id' => 'Hz ID',
        ];
    }
}
