<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_crontab".
 *
 * @property int $id
 * @property int $user_id 执行人
 * @property string $status 执行状态
 * @property string $create_date 创建时间
 * @property string $end_time 结束时间
 * @property string $result_name 结果文件名
 */
class Crontab extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_crontab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','status'], 'integer'],
            [['create_date', 'end_time'], 'safe'],
            [[ 'result_name'], 'string', 'max' => 255],
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
            'status' => 'Status',
            'create_date' => 'Create Date',
            'end_time' => 'End Time',
            'result_name' => 'Result Name',
        ];
    }
}
