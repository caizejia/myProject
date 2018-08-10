<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_track_log".
 *
 * @property string $id 自增id
 * @property string $order_id 订单id
 * @property string $track_date 日期
 * @property string $track_status 物流状态
 * @property string $freight_forwarder 货代
 * @property string $landing_company 落地配公司
 * @property string $md5 防止重复MD5
 * @property string $remark 备注
 * @property string $destination 目的地
 * @property string $mailing 详情
 * @property string $number 物流单号
 */
class TrackLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_track_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'md5'], 'required'],
            [['order_id'], 'integer'],
            [['track_date'], 'safe'],
            [['track_status'], 'string'],
            [['freight_forwarder', 'landing_company', 'md5'], 'string', 'max' => 32],
            [['remark', 'destination', 'mailing', 'number'], 'string', 'max' => 255],
            [['md5'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'track_date' => 'Track Date',
            'track_status' => 'Track Status',
            'freight_forwarder' => 'Freight Forwarder',
            'landing_company' => 'Landing Company',
            'md5' => 'Md5',
            'remark' => 'Remark',
            'destination' => 'Destination',
            'mailing' => 'Mailing',
            'number' => 'Number',
        ];
    }
}
