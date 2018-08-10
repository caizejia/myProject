<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_problems".
 *
 * @property string $id
 * @property string $order_id
 * @property integer $problem
 * @property integer $status
 * @property string $create_date
 * @property string $description
 * @property string $track_number
 * @property string $new_price
 */
class WmsProblems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_problems';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'track_number'], 'required'],
            [['order_id', 'problem', 'status'], 'integer'],
            [['create_date'], 'safe'],
            [['description'], 'string'],
            [['new_price'], 'number'],
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
            'order_id' => '订单ID',
            'problem' => 'Problem',
            'status' => 'Status',
            'create_date' => 'Create Date',
            'description' => 'Description',
            'track_number' => 'Track Number',
            'new_price' => 'New Price',
        ];
    }
}
