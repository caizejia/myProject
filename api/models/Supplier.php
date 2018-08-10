<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_supplier".
 *
 * @property int $id 供应商表id
 * @property string $name 供应商名称
 * @property string $area 地区
 * @property string $platform 平台
 * @property string $purchase_price 采购价
 * @property int $minimum_qty 最少量
 * @property string $link_name 联系人
 * @property string $link_phone 联系手机号
 * @property string $email 邮箱
 * @property int $create_time 创建时间
 * @property int $update_time 更新时间
 * @property int $create_by 创建人
 * @property int $update_by 更新人
 * @property int $is_del 是否删除（0否 1是）
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchase_price'], 'number'],
            [['minimum_quantity', 'create_time', 'update_time', 'create_by', 'update_by'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['area'], 'string', 'max' => 2],
            [['platform'], 'string', 'max' => 32],
            [['link_name'], 'string', 'max' => 16],
            [['link_phone'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 128],
            [['is_del'], 'string', 'max' => 1],
        ];
    }

    /**
     * 配置fun验证字段
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'area', 'platform', 'purchase_price', 'minimum_quantity', 'link_name', 'link_phone', 'email'],
            'update' => ['name', 'area', 'platform'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'area' => 'Area',
            'platform' => 'Platform',
            'purchase_price' => 'Purchase Price',
            'minimum_qty' => 'Minimum Qty',
            'link_name' => 'Link Name',
            'link_phone' => 'Link Phone',
            'email' => 'Email',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'create_by' => 'Create By',
            'update_by' => 'Update By',
            'is_del' => 'Is Del',
        ];
    }

    /**
     * 获取供应商select列表
     */
    public static function selectList()
    {
        $data = self::find()->select('id AS value, name AS label')
            ->where(['is_del' => 0])
            ->asArray()->all();

        return $data;
    }

    /**
     * 供应商对sku 一对多 （暂定）
     */
    public function getProductSku()
    {
        return $this->hasMany(ProductSku::className(), ['supplier_id' => 'id']);
    }
}
