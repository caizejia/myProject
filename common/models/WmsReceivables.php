<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_receivables".
 *
 * @property string $id
 * @property string $ref
 * @property string $rv_money
 * @property string $act_money
 * @property string $balance_money
 * @property integer $ca_id
 * @property integer $ca_type
 * @property string $action_time
 * @property string $create_time
 * @property integer $purchase_id
 * @property integer $pay_user_id
 * @property integer $status
 */
class WmsReceivables extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public static $status = [
        0 => '退款中',
        1 => '退款完成',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_receivables';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ref', 'rv_money', 'act_money', 'balance_money' ], 'required'],
            [['rv_money', 'act_money', 'balance_money'], 'number'],
            [['purchases_detail_id', 'action_user_id', 'status'], 'integer'],
            [['action_time', 'create_time'], 'safe'],
            [['id', 'ref'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref' => '单号',
            'rv_money' => '应收账款总额',
            'act_money' => '已收总额',
            'balance_money' => '未收总额',
            'ca_id' => '往来单位Id 例如  supplier 表的id			',
            'ca_type' => '往来单位分类 0：供应商',
            'action_time' => '收款日期',
            'create_time' => '单据生成日期',
            'purchases_detail_id' => '对应采购单id',
            'action_user_id' => '操作人',
            'status' => '支付状态',
        ];
    }
}
