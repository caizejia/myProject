<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_tw_track_number".
 *
 * @property string $id
 * @property string $track_number
 * @property string $order_id
 */
class WmsTwTrackNumber extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_tw_track_number';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['track_number'], 'required'],
            [['order_id'], 'integer'],
            [['track_number'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'track_number' => '运单号',
            'order_id' => 'Order ID',
        ];
    }
}
