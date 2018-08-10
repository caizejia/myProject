<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_cds_key".
 *
 * @property string $id
 * @property string $order_id
 */
class WmsCdsKey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_cds_key';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
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
        ];
    }
}
