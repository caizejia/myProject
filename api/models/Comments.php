<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_comments".
 *
 * @property string $id
 * @property string $pid 产品id
 * @property string $name 名字
 * @property string $mobile 联系电话
 * @property int $star 评分
 * @property string $comment 评论
 * @property string $country 国家
 * @property int $is_del 是否删除 0：否；1：是
 * @property string $create_time 创建时间
 * @property string $create_by 评论人员id
 * @property string $ip ip地址
 * @property int $type 评论人员类型  0：内部；1：外部；
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'name', 'mobile', 'country'], 'required'],
            [['pid', 'create_time', 'create_by'], 'integer'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['mobile', 'ip'], 'string', 'max' => 32],
            [['star', 'is_del', 'type'], 'string', 'max' => 1],
            [['country'], 'string', 'max' => 2],
        ];
    }

     /**
     * 配置验证字段
     */
    public function scenarios()
    {
        return [
            'create' => ['pid', 'name', 'mobile', 'star','country', 'comment'],
            'update' => ['pid', 'name', 'mobile', 'star','country', 'comment'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'star' => 'Star',
            'comment' => 'Comment',
            'country' => 'Country',
            'is_del' => 'Is Del',
            'create_time' => 'Create Time',
            'create_by' => 'Create By',
            'ip' => 'Ip',
            'type' => 'Type',
        ];
    }
}
