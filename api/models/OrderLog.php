<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_order_log".
 *
 * @property string $id 订单日志表id
 * @property string $oid 订单id
 * @property string $action_user 操作人
 * @property int $old_status 原订单状态
 * @property int $new_status 新订单状态
 * @property string $remark 备注
 * @property string $action_ip 操作ip
 * @property string $log_time 记录时间
 */
class OrderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_order_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oid', 'action_user', 'log_time', 'old_status', 'new_status'], 'integer'],
            [['action_ip'], 'string', 'max' => 32],
            [['remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'oid' => 'Oid',
            'action_user' => 'Action User',
            'old_status' => 'Old Status',
            'new_status' => 'New Status',
            'remark' => 'Remark',
            'action_ip' => 'Action Ip',
            'log_time' => 'Log Time',
        ];
    }
}
