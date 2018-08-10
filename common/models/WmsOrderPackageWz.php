<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_order_package_wz".
 *
 * @property string $id
 * @property string $order_id
 * @property string $weight
 * @property string $length
 * @property string $width
 * @property string $height
 */
class WmsOrderPackageWz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_order_package_wz';
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


    

    public function length($id)
    {
        $product = self::find()->select(['length'])->where("order_id=$id")->one();
        return $product['length'];
    }

    public function width($id)
    {
        $product = self::find()->select(['width'])->where("order_id=$id")->one();
        return $product['width'];
    }

    public function height($id)
    {
        $product = self::find()->select(['height'])->where("order_id=$id")->one();
        return $product['height'];
    }

    public function weight($id)
    {
        $product = self::find()->select(['weight'])->where("order_id=$id")->one();
        return $product['weight'];
    }
}
