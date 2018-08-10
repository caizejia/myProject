<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_warehouse".
 *
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string $country
 * @property string $mobile
 * @property string $address
 * @property string $type
 * @property bool $istran 是否转运
 */
class WmsWarehouse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_warehouse';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['istran'], 'boolean'],
            [['code', 'name', 'country', 'mobile', 'address'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '仓库号码',
            'name' => '仓库名称',
            'country' => '所在国家',
            'mobile' => '联系电话',
            'address' => '地址',
            'type' => '类型',
            'istran' => '是否转运',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
}
