<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_reviews".
 *
 * @property string $id
 * @property string $product_id
 * @property string $name
 * @property string $mobile
 * @property int $star
 * @property string $comment
 * @property string $re_country
 * @property int $status 状态
 * @property int $product_ids
 * @property string $create_date
 */
class Reviews extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'name', 'mobile'], 'required'],
            [['product_id', 'star', 'status', 'product_ids'], 'integer'],
            [['comment'], 'string'],
            [['create_date'], 'safe'],
            [['name', 'mobile', 're_country'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'star' => 'Star',
            'comment' => 'Comment',
            're_country' => 'Re Country',
            'status' => 'Status',
            'product_ids' => 'Product Ids',
            'create_date' => 'Create Date',
        ];
    }
}
