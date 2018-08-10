<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_sub_inventory_locklog".
 *
 * @property string $id
 * @property string $sub_inventory_id 库位ID
 * @property string $goods_id goods_id
 * @property string $sku
 * @property int $number 变动的数量
 * @property int $lock_count 锁定数量
 * @property int $type 1: 入库区，2：出库区
 * @property string $comment 备注
 * @property string $create_time 操作时间
 * @property string $ref 单据编号
 * @property int $userid 操作人
 */
class SubInventoryLocklog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_sub_inventory_locklog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sub_inventory_id', 'goods_id', 'number', 'lock_count', 'type', 'create_time', 'userid'], 'required'],
            [['sub_inventory_id', 'goods_id', 'number', 'lock_count', 'type', 'userid'], 'integer'],
            [['create_time'], 'safe'],
            [['sku', 'ref'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sub_inventory_id' => 'Sub Inventory ID',
            'goods_id' => 'Goods ID',
            'sku' => 'Sku',
            'number' => 'Number',
            'lock_count' => 'Lock Count',
            'type' => 'Type',
            'comment' => 'Comment',
            'create_time' => 'Create Time',
            'ref' => 'Ref',
            'userid' => 'Userid',
        ];
    }
}
