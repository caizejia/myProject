<?php

namespace api\models;

use Yii;
use api\components\Funs;

/**
 * This is the model class for table "oms_product_sku".
 *
 * @property int $id sku表id
 * @property int $pid 产品id
 * @property string $sku_code sku货码
 * @property string $sku_attribute 属性值id列表
 * @property int $product_stock 库存数量
 * @property string $price 价格
 * @property string $image 单图选择时展示
 * @property int $create_time 创建时间
 * @property int $update_time 更新时间
 * @property int $create_by 创建人
 * @property int $update_by 更新人
 * @property int $is_del 是否删除（0否 1是）
 */
class ProductSku extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_product_sku';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid'], 'required'],
            [['pid', 'create_time', 'update_time', 'create_by', 'update_by', 'is_del'], 'integer'],
            [['sku_code'], 'string', 'max' => 32],
            [['sku_attribute'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'sku_code' => 'Sku Code',
            'sku_attribute' => 'Sku Attribute',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'create_by' => 'Create By',
            'update_by' => 'Update By',
            'is_del' => 'Is Del',
        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'],
            'create' => ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'],
            'update' => ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'],
        ];
    }

    /**
     * sku对产品 一对一
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'pid']);
    }

    /**
     * 处理属性组合集合 批量入库
     * @author YXH 
     * @date 2018/06/25
     */
    public function add($psData)
    {
        $field = ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'];
        $time = time();
        $addData = $arr = [];
        $list = $psData['attr_list'];
        $attrData = Funs::get_arr_set($list);
        foreach ($attrData as $v) {
            $temp = [];
            foreach ($v as $node) {
                $temp[$node['attr']] = $node['value'];
            }
            $addData[] = [
                $psData['pid'],
                'sku000',
                json_encode($temp, JSON_UNESCAPED_UNICODE),
                $time,
                $psData['uid'],
            ];
        }
        // 一次批量插入
        $tableName = self::tableName();
        $successNum = Yii::$app->db->createCommand()->batchInsert($tableName,$field,$addData)->execute();

        return $successNum;
    }

    public function getSkuList($fields, $where)
    {
        $data = self::find()
            ->where($where)
            ->select($fields)
            ->asArray()->all();

        return $data;
    }

    
}
