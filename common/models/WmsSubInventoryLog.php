<?php

namespace common\models;

use Yii;
use common\models\WmsSubInventory;
use common\models\WmsProductDetails;
/**
 * This is the model class for table "wms_sub_inventory_log".
 *
 * @property string $sub_inventory_id 库位ID
 * @property string $goods_id goods_id
 * @property string $sku
 * @property string $number 变动的数量
 * @property string $type 1: 入库区，2：出库区
 * @property string $comment 备注
 * @property string $create_time
 * @property string $ref 变动单号
 */
class WmsSubInventoryLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_sub_inventory_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sub_inventory_id', 'goods_id', 'number', 'type', 'create_time'], 'required'],
            [['sub_inventory_id', 'goods_id', 'number', 'type'], 'integer'],
            [['create_time'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['sku'], 'string', 'max' => 50],
            [['comment', 'ref'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sub_inventory_id' => '库位ID',
            'goods_id' => 'goods_id',
            'number' => '变动的数量',
            'type' => '1: 入库区，2：出库区',
            'comment' => '备注',
            'create_time' => 'Create Time',
            'sku' => 'Sku',
            'ref' => 'Ref',
        ];
    }

    //定义api返回字段
//    public function fields()
//    {
//        $details = new WmsProductDetails();
//        return [
//            'id',
//            'sub_inventory'=> function ($model) {
//            //???
//                return $model->subinv->code ;
//            },
//            'goods_id',
//            'sku',
//            /*'sku' => function ($model) use($details) {
//
//                $good = $details::getSku($model->goods_id);
//                return $good->sku ;
//            },*/
//            'number',
//            'type',
//            /*'type' => function ($model) {
//                $name = '未知';
//                if($model->type==1){
//                    $name = '入库区';
//                }else{
//                    $name = '出库区';
//                }
//                return $name ;
//            },*/
//            'create_time'
//        ];
//    }

    public function getSubinv()
    {
        return $this->hasOne(WmsSubInventory::className(), ['id' => 'sub_inventory_id']);
    }

}
