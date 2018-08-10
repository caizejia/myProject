<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_pick_round".  拣货单
 *
 * @property int $id
 * @property string $ref 单号 波次
 * @property string $create_time 生成时间
 * @property string $warehouse_id 仓库
 * @property string $print_time 打印次数
 * @property string $num 订单量
 * @property int $status 状态 0：新建 1：拣货中 2：完成
 * @property string $user_id 打印人
 * @property string $picking_time 打印时间
 * @property string $lc 承运商
 */
class WmsPickRound extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_pick_round';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'warehouse_id', 'print_time', 'num', 'status', 'picking_time'], 'required'],
            [['create_time', 'picking_time'], 'safe'],
            [['warehouse_id', 'print_time', 'num', 'status', 'user_id'], 'integer'],
            [['ref', 'lc'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref' => '单号 波次',
            'create_time' => '生成时间 ',
            'warehouse_id' => '仓库',
            'print_time' => '打印次数',
            'num' => '订单量',
            'status' => '状态 0：新建 1：拣货中 2：完成',
            'lc' => '承运商',
        ];
    }
}
