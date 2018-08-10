<?php

namespace common\models;

use Yii;
use common\models\WmsProductDetails;
use common\models\WmsInventory1;
/**
 * This is the model class for table "wms_sub_inventory_sku".
 *
 * @property string $id
 * @property integer $goods_id
 * @property string $sku
 * @property integer $sub_inventory_id
 * @property string $number
 * @property string $lock_count 锁定数量
 */
class WmsSubInventorySku extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_sub_inventory_sku';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'sub_inventory_id', 'number', 'lock_count'], 'required'],
            [['goods_id', 'sub_inventory_id', 'number', 'lock_count'], 'integer'],
            [['sku'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品id',
            'sku' => 'Sku',
            'sub_inventory_id' => '库区id',
            'number' => '数量',
            'lock_count' => 'Lock Count',
        ];
    }

    //货物从库位 出入 $type 1: 入库区，2：出库区
    public function in_or_out($sku,$sub_inventory_id,$num,$type){
        $wms_product_details = new WmsProductDetails();
        $good_info = $wms_product_details::getGoodsId($sku);    
        $goods_id =  $good_info['id']; 

        $sub_inventory_sku = $this->findByGoodsid($sub_inventory_id,$goods_id);
        
        if($num<=0){
            return false;
        }
        if($sub_inventory_sku){//添加更新
            if($type==1){//库位增加产品
                $val = ['number' => $sub_inventory_sku['number']+$num ];
            }elseif($type==2){//库位减少产品
                $val = ['number' => $sub_inventory_sku['number']-$num ];
                if($val['number']<0){
                    return false;
                }
            }
            $inventory_status =  \Yii::$app->db->createCommand()->update('wms_sub_inventory_sku', $val,"id = $sub_inventory_sku[id]")->execute();
            
        }else{//新增一条sku库位记录
            if($type==1){
                $val = ['goods_id' => $goods_id, 'sku' => $sku, 'sub_inventory_id' => $sub_inventory_id, 'number' => $num ];
                $inventory_status =  \Yii::$app->db->createCommand()->insert('wms_sub_inventory_sku', $val)->execute();
            }else{
                return false;
            }
        }
        //记录明细 
        $val = [ 'sub_inventory_id' => $sub_inventory_id, 'goods_id' => $goods_id, 'sku' => $sku, 'number' => $num ,'type' => $type ,'create_time' => date('Y-m-d H:i:s')];
        $wms_sub_inventory_log =  \Yii::$app->db->createCommand()->insert('wms_sub_inventory_log', $val)->execute();
        return $inventory_status;
    }


    //入库    TODO  pda 采购入库，应该要通知 sku的库存inventory改变数量，通知采购单 入库数量和状态， 通知订单 已经采购完成 等等。 采购退货同理。 
    public  function inByPda($warehouse_id,$goods_id, $num,$ref_type=1)
    {
        //入库
        $WmsInventory = new WmsInventory1();
        $WmsInventory->inItem($warehouse_id,$goods_id, $num,$ref_type );
        //改变采购单状态 TODO
    }
    //出库     TODO 销售出库， 应该要通知 sku的库存inventory改变数量，通知出库单 出库数量和状态， 通知订单 已经出库完成 等等。
    public  function outByPda($warehouse_id,$goods_id, $num,$ref_type=3)
    {
        //入库
        $WmsInventory = new WmsInventory1();
        $WmsInventory->outItem($warehouse_id,$goods_id, $num,$ref_type );
        //改变订单状态 TODO
    }


    public static function findByGoodsid($sub_inventory_id,$goods_id)
    {
        return static::findOne(['sub_inventory_id' => $sub_inventory_id,'goods_id' => $goods_id]);
    }
}
