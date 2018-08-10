<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_problems_log".
 *
 * @property string $id
 * @property string $problems_id
 * @property integer $action_type
 * @property string $remarks
 * @property string $action_tpl
 * @property string $feedback
 * @property string $action_date
 * @property integer $is_download
 */
class WmsProblemsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_problems_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['problems_id', 'action_type', 'action_date'], 'required'],
            [['problems_id', 'action_type', 'is_download'], 'integer'],
            [['remarks', 'feedback'], 'string'],
            [['action_date'], 'safe'],
            [['action_tpl'], 'string', 'max' => 255],
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
            'action_type' => '处理方式 1邮件 2电话',
            'remarks' => 'Remarks',
            'action_tpl' => 'Action Tpl',
            'feedback' => 'Feedback',
            'action_date' => 'Action Date',
            'is_download' => 'Is Download',
        ];
    }
}
