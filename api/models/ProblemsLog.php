<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_problems_log".
 *
 * @property string $id
 * @property string $problems_id
 * @property int $action_type 处理方式 1邮件 2电话
 * @property string $remarks
 * @property string $action_tpl
 * @property string $feedback
 * @property string $action_date
 * @property int $is_download 是否已下载
 */
class ProblemsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_problems_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['problems_id', 'action_type', 'action_date'], 'required'],
            [['problems_id'], 'integer'],
            [['remarks', 'feedback'], 'string'],
            [['action_date'], 'safe'],
            [['action_type'], 'string', 'max' => 2],
            [['action_tpl'], 'string', 'max' => 255],
            [['is_download'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'problems_id' => 'Problems ID',
            'action_type' => 'Action Type',
            'remarks' => 'Remarks',
            'action_tpl' => 'Action Tpl',
            'feedback' => 'Feedback',
            'action_date' => 'Action Date',
            'is_download' => 'Is Download',
        ];
    }
}
