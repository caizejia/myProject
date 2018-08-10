<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_sales_city".
 *
 * @property string $id 自增id
 * @property string $city_code 城市2字码
 * @property string $city_name 城市名称
 * @property string $exchange_rate 汇率
 * @property int $is_del 是否启用（0否 1是）
 */
class SalesCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_sales_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exchange_rate','is_del'], 'number'],
            [['city_code'], 'string', 'max' => 2],
            [['city_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_code' => 'City Code',
            'city_name' => 'City Name',
            'exchange_rate' => 'Exchange Rate',
            'is_del' => 'Is Del',
        ];
    }
}
