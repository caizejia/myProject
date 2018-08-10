<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_order_goods".
 *
 * @property string $id 订单商品列表
 * @property string $oid 订单id
 * @property string $pid 产品id
 * @property string $sku_code sku货码
 * @property int $number 数量
 * @property string $price 价格
 * @property string $pname 产品名字
 * @property string $size 尺寸名字
 * @property string $color 颜色名字
 * @property string $img 图片链接
 * @property int $create_time 添加时间
 * @property string $update_time 更新时间
 * @property string $update_by 更新人
 * @property int $is_del 是否删除（0否 1是）
 */
class OrderGoods extends \yii\db\ActiveRecord
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
