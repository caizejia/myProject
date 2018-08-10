<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_promotion".
 *
 * @property int $id 促销信息表id
 * @property int $s_r_id sku发布关联表id
 * @property string $sku_id 产品sku表id
 * @property int $number 数量
 * @property int $type 促销类型（1组合销售 2第二件优惠价 3买一送几 4赠品）
 * @property string $price 价格 
 * @property int $create_time 创建时间
 * @property int $is_del 是否删除（0否 1是）
 */
class Promotion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_promotion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 's_r_id', 'number', 'type', 'create_time', 'is_del'], 'integer'],
            [['price'], 'number'],
            [['sku_id'], 'string', 'max' => 64],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            's_r_id' => 'S R ID',
            'sku_id' => 'Sku ID',
            'number' => 'Number',
            'type' => 'Type',
            'price' => 'Price',
            'create_time' => 'Create Time',
            'is_del' => 'Is Del',
        ];
    }

    public function add($data, $id)
    {
        $field = ['s_r_id', 'sku_id', 'number', 'type', 'price'];
        $addData = [];
        foreach ($data as $v) {
            if ($v['type'] == 1 || $v['type'] == 3) {
                $temp = [
                    $id,
                    $v['id'],
                    $v['params'],
                    $v['type'],
                    $v['params'],
                ];
            } elseif ($v['type'] == 2) {
                $arr = explode(',', $v['params']);
                $temp = [
                    $id,
                    $arr[0],
                    $arr[1],
                    $v['type'],
                    0,
                ];
            }
            
            $addData[] = $temp;
        }

        $tableName = self::tableName();
        $successNum = Yii::$app->db->createCommand()->batchInsert($tableName,$field,$addData)->execute();

        return $successNum;
    }
}
