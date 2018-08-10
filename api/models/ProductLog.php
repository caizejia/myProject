<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_product_log".
 *
 * @property string $id 订单日志表id
 * @property string $pid 产品id
 * @property string $action_user 操作人
 * @property int $action 操作功能
 * @property string $remark 备注
 * @property string $action_ip 操作ip
 * @property string $log_time 记录时间
 */
class ProductLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_product_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'action_user', 'action_ip', 'log_time'], 'integer'],
            [['action'], 'string', 'max' => 1],
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
            'pid' => 'Pid',
            'action_user' => 'Action User',
            'action' => 'Action',
            'remark' => 'Remark',
            'action_ip' => 'Action Ip',
            'log_time' => 'Log Time',
        ];
    }
}
