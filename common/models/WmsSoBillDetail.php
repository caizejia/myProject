<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_so_bill_detail".
 *
 * @property string $id
 * @property string $oid
 * @property string $pid
 * @property string $sku_code
 * @property integer $number
 * @property string $price
 * @property string $attributes1
 * @property string $attributes2
 * @property string $update_time
 * @property string $update_by
 * @property integer $is_del
 */
class WmsSoBillDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_order_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid', 'pid', 'number', 'create_time', 'update_time', 'update_by'], 'integer'],
            [['price'], 'number'],
            [['sku_code'], 'string', 'max' => 20],
            [['pname', 'color', 'img'], 'string', 'max' => 255],
            [['size'], 'string', 'max' => 32],
            [['is_del'], 'string', 'max' => 1],
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
            'pid' => 'Pid',
            'sku_code' => 'Sku Code',
            'number' => 'Number',
            'price' => 'Price',
            'pname' => 'Pname',
            'size' => 'Size',
            'color' => 'Color',
            'img' => 'Img',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'update_by' => 'Update By',
            'is_del' => 'Is Del',
        ];
    }
}
