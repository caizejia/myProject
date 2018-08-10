<?php

namespace common\models;

use Yii;

/** 
 * This is the model class for table "wms_pick_round_detail". 拣货单详情
 *
 * @property integer $id
 * @property string $pick_round_id
 * @property string $so_bill_id
 * @property string $order_no
 * @property string $order_info
 */
class WmsPickRoundDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_pick_round_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pick_round_id', 'so_bill_id', 'order_no', 'order_info'], 'required'],
            [['pick_round_id', 'so_bill_id'], 'integer'],
            [['order_info'], 'string'],
            [['order_no'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pick_round_id' => '主表id',
            'so_bill_id' => '对应订单id',
            'order_no' => '订单号',
            'order_info' => '压缩的发货信息，json格式',
        ];
    }

    //定义api返回字段
    public function fields()
    {
        return [ 
            'id', 
            'pick_round_id', 
            'so_bill_id', 
            'order_no', 
            'order_info' => function ($model) {
                $order_info = json_decode($model->order_info,1);
                foreach ($order_info as $key => $value) {
                    $obj['sku'] = $value['sku_code'];
                    $obj['number'] = $value['number'];
                    $obj['code'] = $value['code'];
                    $ret[] = $obj ; 
                }
                
                return $ret ;
            },
        ];
    }

}
