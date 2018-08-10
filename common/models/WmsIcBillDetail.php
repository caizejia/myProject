<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_ic_bill_detail".
 *
 * @property string $id
 * @property string $create_time
 * @property string $sku
 * @property string $spu
 * @property string $goods_id wms_goods id
 * @property string $goods_count 盘点后库存数量
 * @property string $ic_bill_id 对应盘点id
 * @property string $difcount 差异数据流
 */
class WmsIcBillDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_ic_bill_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time', 'goods_id', 'goods_count', 'ic_bill_id', 'difcount'], 'required'],
            [['create_time'], 'safe'],
            [['goods_id', 'ic_bill_id', 'difcount'], 'integer'],
            [['goods_count'], 'number'],
            [['sku', 'spu'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_time' => 'Create Time',
            'sku' => 'Sku',
            'spu' => 'Spu',
            'goods_id' => 'Goods ID',
            'goods_count' => 'Goods Count',
            'ic_bill_id' => 'Ic Bill ID',
            'difcount' => 'Difcount',
        ];
    }
}
