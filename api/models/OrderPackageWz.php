<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_order_package_wz".
 *
 * @property string $id
 * @property string $order_id
 * @property string $weight
 * @property string $length
 * @property string $width
 * @property string $height
 */
class OrderPackageWz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_order_package_wz';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
            [['weight', 'length', 'width', 'height'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'weight' => 'Weight',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
        ];
    }
}
