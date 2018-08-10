<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_attribute_key".
 *
 * @property string $id 自增id
 * @property string $aid 产品属性表id
 * @property string $language 语言（CN中文 EN英文。。。）
 * @property string $name 名称
 */
class AttributeKey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_attribute_key';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid','create_time'], 'integer'],
            [['language'], 'string', 'max' => 2],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'aid' => 'Aid',
            'language' => 'Language',
            'name' => 'Name',
        ];
    }

    /**
     * 配置验证场景
     */
    public function scenarios()
    {
        return [
            'update' => ['name'],
        ];
    }

    /**
     * 获取vue checkbox 选择列表
     */
    public static function checkList()
    {
        $data = self::find()->select('id AS value, name AS label')
            ->where(['is_del' => 0, 'language' => 'CN'])
            ->asArray()->orderBy('sort DESC')->all();

        return $data;
    }

}
