<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_sku_release_relation".
 *
 * @property int $id 关联表id
 * @property int $sku_id sku表id
 * @property string $sku_code sku货码
 * @property int $p_r_id 发布表id
 * @property int $promotion_type 促销类型（0无 1组合销售 2第二件优惠价 3买一赠几 4赠品）
 * @property string $attr_one 规格属性json
 * @property string $img 单品图片
 * @property string $price 价格
 * @property int $is_top 是否上架（0否 1是）
 */
class SkuReleaseRelation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_sku_release_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sku_id', 'p_r_id', 'promotion_type', 'is_top'], 'integer'],
            [['sku_code'], 'required'],
            [['price'], 'number'],
            [['sku_code'], 'string', 'max' => 32],
            [['attr_one'], 'string', 'max' => 128],
            [['img'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku_id' => 'Sku ID',
            'sku_code' => 'Sku Code',
            'p_r_id' => 'P R ID',
            'promotion_type' => 'Promotion Type',
            'attr_one' => 'Attr One',
            'img' => 'Img',
            'price' => 'Price',
            'is_top' => 'Is Top',
        ];
    }

    public function add($data, $id)
    {
        $field = ['sku_id', 'sku_code', 'p_r_id', 'promotion_type', 'attr_one', 'price'];
        $addData = [];
        foreach ($data as $v) {
            $temp = [
                $v['id'],
                $v['sku_code'],
                $id,
                $v['type'] ?? 0,
                $v['sku_attribute'],
                $v['input'],
            ];
            $addData[] = $temp;
        }

        $tableName = self::tableName();
        $successNum = Yii::$app->db->createCommand()->batchInsert($tableName,$field,$addData)->execute();

        return $successNum;
    }

    public static function getSkuList($fields, $where)
    {
        $data = self::find()
            ->where($where)
            ->select($fields)
            ->asArray()->all();

        return $data;
    }
}
