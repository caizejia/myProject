<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_brand".
 *
 * @property int $id 品牌表id
 * @property int $cid 所属分类id
 * @property string $name 品牌名称
 * @property string $logo logo图片
 * @property int $create_time 创建时间
 * @property int $update_time 更新时间
 * @property int $create_by 创建人
 * @property int $update_by 更新人
 * @property int $is_del 是否删除（0否 1是）
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'create_time', 'update_time', 'create_by', 'update_by'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['logo'], 'string', 'max' => 128],
            [['is_del'], 'string', 'max' => 1],
        ];
    }

    /**
     * 配置验证字段
     */
    public function scenarios()
    {
        return [
            'create' => ['cid', 'name', 'logo'],
            'update' => ['cid', 'name', 'logo'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => 'Cid',
            'name' => 'Name',
            'logo' => 'Logo',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'create_by' => 'Create By',
            'update_by' => 'Update By',
            'is_del' => 'Is Del',
        ];
    }

    /**
     * 品牌对分类 一对一
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'cid']);
    }

    /**
     * 获取品牌列表
     */
    public static function selectList()
    {
        $data = self::find()->select('id AS value, name AS label')
            ->where(['is_del' => 0])
            ->asArray()->orderBy('sort DESC')->all();

        return $data;
    }
}
