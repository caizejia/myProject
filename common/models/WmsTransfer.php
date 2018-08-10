<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_transfer".
 *
 * @property string $id
 * @property string $ref 单据编号
 * @property string $action_time 业务发生日期
 * @property int $action_user_id 业务操作员
 * @property string $wfrom_id 调出仓库
 * @property string $wto_id 调入仓库
 * @property int $hzfrom_id 调出货主
 * @property int $hzto_id 收货货主
 * @property int $status 单据状态,0草稿，1提交
 * @property int $outstatus 出库状态  0:待出库 1 ：已出库
 * @property int $instatus 入库状态 0:待入库 1 ：已入库
 * @property string $remark 备注
 */
class WmsTransfer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_transfer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action_time'], 'required'],
            [['action_time'], 'safe'],
            [['action_user_id', 'hzfrom_id', 'hzto_id', 'status', 'outstatus', 'instatus'], 'integer'],
            [['ref', 'wfrom_id', 'wto_id'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref' => 'Ref',
            'action_time' => 'Action Time',
            'action_user_id' => 'Action User ID',
            'wfrom_id' => 'Wfrom ID',
            'wto_id' => 'Wto ID',
            'hzfrom_id' => 'Hzfrom ID',
            'hzto_id' => 'Hzto ID',
            'status' => 'Status',
            'outstatus' => 'Outstatus',
            'instatus' => 'Instatus',
            'remark' => 'Remark',
        ];
    }
}
