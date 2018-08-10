<?php

namespace common\models;
use common\models\Adminuser;
use common\models\WmsWarehouse;

use Yii;

/**
 * This is the model class for table "wms_inventory_detail".
 *
 * @property string $id
 * @property string $ref 单号
 * @property string $ref_type '0 库存建账'、'1 采购入库'、'2 采购退货出库'、'3 销售出库'、'4 销售退货入库'、'5 库存盘点-盘盈入库'、'6 库存盘点-盘亏出库','7'=>' 库存调拨-出库',
 * @property string $goods_id 产品id
 * @property string $balance_count 商品结余数量
 * @property string $balance_money 商品结余金额
 * @property string $balance_price 商品结余单价
 * @property string $in_count 商品入库数量
 * @property string $in_money 商品入库金额
 * @property string $in_price 商品入库单价
 * @property string $out_count 商品出库数量
 * @property string $out_money 商品出库金额
 * @property string $out_price 商品出库单价
 * @property string $action_user_id 操作人
 * @property string $warehouse_id 仓库id
 * @property string $create_time 出入库存时间
 * @property int $hz_id 货主ID
 * @property string $hw_id 货位id
 */
class WmsInventoryDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_inventory_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref_type', 'goods_id', 'balance_count', 'balance_money', 'balance_price', 'in_count', 'in_money', 'in_price', 'out_count', 'out_money', 'out_price', 'action_user_id', 'warehouse_id', 'create_time', 'hz_id', 'hw_id'], 'required'],
            [['goods_id', 'balance_count', 'in_count', 'out_count', 'action_user_id', 'warehouse_id', 'hz_id', 'hw_id'], 'integer'],
            [['balance_money', 'balance_price', 'in_money', 'in_price', 'out_money', 'out_price'], 'number'],
            [['create_time'], 'safe'],
            [['ref', 'ref_type'], 'string', 'max' => 255],
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
            'ref_type' => '\'0 库存建账\'、\'1 采购入库\'、\'2 采购退货出库\'、\'3 销售出库\'、\'4 销售退货入库\'、\'5 库存盘点-盘盈入库\'、\'6 库存盘点-盘亏出库\'',
            'goods_id' => '产品id',
            'balance_count' => '商品结余数量',
            'balance_money' => '商品结余金额',
            'balance_price' => '商品结余单价',
            'in_count' => '商品入库数量',
            'in_money' => '商品入库金额',
            'in_price' => '商品入库单价',
            'out_count' => '商品出库数量',
            'out_money' => '商品出库金额',
            'out_price' => '商品出库单价',
            'action_user_id' => '0 默认是系统',
            'warehouse_id' => '仓库id',
            'create_time' => '出入库存时间',
        ];
    }

//    public static  $ref_type = ['0' =>'库存建账','1'=> '采购入库','2'=> '采购退货出库','3'=> '销售出库','4'=> '销售退货入库','5'=>'库存盘点-盘盈入库','6'=>'库存盘点-盘亏出库'];


    //定义api返回字段
//    public function fields()
//    {
//        return [
//            'id',
//            'ref',
//            'ref_type' ,
//            'goods_id'  ,
//            'balance_count' ,
//            'balance_money'  ,
//            'balance_price' ,
//            'in_count'  ,
//            'in_money' ,
//            'in_price' ,
//            'out_count' ,
//            'out_money' ,
//            'out_price'  ,
//            'warehouse_id',
//            'action_user_id',
////            'action_user_name' => function ($model) {
////                $name = Adminuser::getUsername($model->action_user_id);
////                return $name ;
////            },
////            'warehouse_name' => function ($model) {
////                $name = WmsWarehouse::findOne($model->warehouse_id);
////                return $name['name'] ;
////            },
//            'create_time',
//            'hz_id',
//            'hw_id',
//
//
//
//        ];
//    }
}
