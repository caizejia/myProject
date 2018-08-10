<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_ad_website".
 *
 * @property string $id
 * @property string $name 网站名称
 * @property string $url 网站url
 * @property string $create_time 创建时间
 * @property string $create_by 创建人
 * @property int $is_del 是否删除（0否 1是）
 */
class AdWebsite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_ad_website';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'create_by'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['url'], 'string', 'max' => 128],
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
            'name' => 'Name',
            'url' => 'Url',
            'create_time' => 'Create Time',
            'create_by' => 'Create By',
            'is_del' => 'Is Del',
        ];
    }
}
