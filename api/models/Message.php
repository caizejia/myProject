<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_message".
 *
 * @property int $id
 * @property string $user_id
 * @property string $title 标题
 * @property string $message 内容
 * @property string $comment 备注
 * @property string $time 过期时间
 * @property string $create_date 创建时间
 * @property string $is_read 是否阅读   0未阅读 1已阅读
 * @property string $is_del 是否删除 1删除 0正常
 * @property string $create_user 发件人
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['time', 'create_date'], 'safe'],
            [['user_id', 'title', 'comment', 'is_read', 'is_del', 'create_user'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'message' => 'Message',
            'comment' => 'Comment',
            'time' => 'Time',
            'create_date' => 'Create Date',
            'is_read' => 'Is Read',
            'is_del' => 'Is Del',
            'create_user' => 'Create User',
        ];
    }
}
