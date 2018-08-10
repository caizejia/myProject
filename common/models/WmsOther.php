<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_other".
 *
 * @property string $id
 * @property string $ref 单据编号
 * @property string $action_time 业务发生日期
 * @property int $action_user_id 业务操作员
 * @property string $warehouse_id 仓库
 * @property int $hzfrom_id 货主
 * @property int $status 单据状态:0 草稿,1 提交
 * @property string $remark 备注
 */
class WmsOther extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_other';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action_time'], 'required'],
            [['action_time'], 'safe'],
            [['action_user_id', 'hzfrom_id', 'status'], 'integer'],
            [['ref', 'warehouse_id'], 'string', 'max' => 50],
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
            'ref' => '单据编号',
            'action_time' => '业务发生日期',
            'action_user_id' => '业务操作员',
            'warehouse_id' => '仓库',
            'hzfrom_id' => '货主',
            'status' => '单据状态',
            'remark' => '备注',
        ];
    }
}
