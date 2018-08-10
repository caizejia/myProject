<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_ic_bill".
 *
 * @property string $id
 * @property string $ref 单号
 * @property string $create_time 录单时间
 * @property string $input_user_id 录单人
 * @property int $warehouse_id 仓库id
 * @property int $bill_status 盘点状态。0：没盘点，1：已盘点
 * @property string $memo 盘点单备注
 * @property int $bill_type 盘点类型（1、全盘 2、抽盘）
 * @property string $bill_date 盘点日期
 */
class WmsIcBill extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wms_ic_bill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ref', 'create_time', 'input_user_id', 'warehouse_id', 'bill_status', 'bill_type', 'bill_date'], 'required'],
            [['create_time', 'bill_date'], 'safe'],
            [['warehouse_id', 'bill_status', 'bill_type'], 'integer'],
            [['ref', 'input_user_id', 'memo'], 'string', 'max' => 255],
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
            'create_time' => 'Create Time',
            'input_user_id' => 'Input User ID',
            'warehouse_id' => 'Warehouse ID',
            'bill_status' => 'Bill Status',
            'memo' => 'Memo',
            'bill_type' => 'Bill Type',
            'bill_date' => 'Bill Date',
        ];
    }
}
