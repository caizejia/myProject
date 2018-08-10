<?php

namespace common\models;

use Yii;
use common\models\WmsProductDetails;
use common\models\WmsProduct;
/**
 * This is the model class for table "wms_inventory".
 *
 * @property string $id
 * @property string $goods_id 产品id，对应 oms_product_sku的id
 * @property string $balance_count 商品余额数量（包含锁定的）
 * @property string $afloat_count 剩余
 * @property string $lock_count 在途
 * @property string $warehouse_id 锁定
 * @property string $hz_id 货主编号
 */
class WmsInventory1 extends \yii\db\ActiveRecord
{
    //ref_type '0 库存建账'、'1 采购入库'、'2 采购退货出库'、'3 销售出库'、'4 销售退货入库'、'5 库存盘点-盘盈入库'、'6 库存盘点-盘亏出库'、'7 库存调拨-出库'、'8 库存调拨-入库'

    public static $ref_type = [
        '0' =>'库存建账',
        '1'=> '采购入库',
        '2'=> '采购退货出库',
        '3'=> '销售出库',
        '4'=> '销售退货入库',
        '5'=>'库存盘点-盘盈入库',
        '6'=>'库存盘点-盘亏出库',
        '7'=>' 库存调拨-出库',
        '8'=> '库存调拨-入库'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_inventory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['balance_count', 'afloat_count', 'lock_count', 'warehouse_id', 'hz_id'], 'required'],
            [['balance_count', 'afloat_count', 'lock_count', 'warehouse_id', 'hz_id'], 'integer'],
            [['goods_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'wms_goods的id',
            'balance_count' => '商品余额数量',
            'afloat_count' => '在途商品数量',
            'lock_count' => '锁定商品数量',
            'warehouse_id' => 'Warehouse ID',
            'hz_id' => 'Hz ID',
           
        ];
    }
    //定义api返回字段
    public function fields()
    {
        $controller = Yii::$app->controller->action->actionMethod;
        $params = \Yii::$app->request->queryParams;
        switch ($controller){
            case 'actionIndex':
                $filed =  [
                    'id',
                    'goods_id',
                    'balance_count',
                    'afloat_count',
                    'lock_count',
                    'warehouse_id',
                    'hz_id',
                    'warehouse_name' => function ($model) {
                        $name = WmsWarehouse::findOne($model->warehouse_id);  
                        return $name['name'] ;
                    },
//                    'sku' => function ($model) {
//                        if($model->good){
//                            return $model->good->sku_code ;
//                        }else{
//                            return 'none' ;
//                        }
//
//                    },
                    'color' => function ($model) {
                        return $model->good->sku_attribute ;
                    },
                    'size' => function ($model) {
                        return $model->good->sku_attribute ;
                    },
                    'image' => function ($model) {
                        $info = WmsProduct::getInfo($model->good->pid);
                        $img = json_decode($info['img_list'])[0];
                        return $img ;
                    },
                    'spu' => function ($model) {
                        return $model->good->pid ; //现在只有pid,没有spu
                    },
                    'test' => function ($model) {
                        return $model->good->id;
                    }
                ];
                break;
            case 'actionTest':
                $filed = [
                    'ppp' => function ($model) use($params){
                        return $params['test'];
                    },
                    'sortTest' => function($model){
                        return $model->good->id - $model->id + 1;
                    },
//                    [
//                        'label' => 'id',
//                        'value' => function ($data) {
//                            return $data->id;
//                        },
//                        'format' => 'html'
//                    ],
                ];
            default:
                break;
        }
        return $filed;
    }



    public function getGood()
    {
        return $this->hasOne(WmsProductDetails::className(), ['id'=>'goods_id']);
    }





    
    //建账（初始化库存）
    public  function initItem($warehouse_id,$goods_id, $num)
    {
        $ref_type=0;
        $inventory = $this->findByGoodsid($warehouse_id,$goods_id);
        $wms_product_details = new WmsProductDetails();
        $good = $wms_product_details::getFullInfo($goods_id);     
        if($num<0){
            return false;
        }
        if($inventory){//添加更新
            //增加库存
            $val = ['balance_count' => $num ];
            $inventory_status =  \Yii::$app->db->createCommand()->update('wms_inventory', $val,"id = $inventory[id]")->execute();
            
        }else{//新建一条sku库存记录
            $val = ['goods_id' => $goods_id, 'balance_count' => $num, 'afloat_count' => 0, 'lock_count' => 0,'warehouse_id' => $warehouse_id ];
            $inventory_status =  \Yii::$app->db->createCommand()->insert('wms_inventory', $val)->execute();
        }
        //记录明细 
        $val = ['ref' => 'id'.date('YmdHis'), 'ref_type' => $ref_type, 'goods_id' => $goods_id, 'in_count' => $num ,'warehouse_id' => $warehouse_id ,'create_time' => date('Y-m-d H:i:s'),'balance_count' => $num ];
        $wms_inventory_detail =  \Yii::$app->db->createCommand()->insert('wms_inventory_detail', $val)->execute();
        return $wms_inventory_detail;
    }


    //采购付款 增加在途   
    public  function inAfloat($warehouse_id,$goods_id, $num )
    {
        $inventory = $this->findByGoodsid($warehouse_id,$goods_id);
        $wms_product_details = new WmsProductDetails();
        $good = $wms_product_details::getFullInfo($goods_id);
        if($num<0){
            return false;
        }
        if($inventory){//添加更新
            //增加库存
            $val = ['afloat_count' => $inventory['afloat_count']+$num ];
            $inventory_status =  \Yii::$app->db->createCommand()->update('wms_inventory', $val,"id = $inventory[id]")->execute();
            
        }else{//新增一条sku库存记录
            $val = ['goods_id' => $goods_id, 'balance_count' =>0, 'afloat_count' => $num, 'lock_count' => 0,'warehouse_id' => $warehouse_id ];
            $inventory_status =  \Yii::$app->db->createCommand()->insert('wms_inventory', $val)->execute();
        }
         
        return $inventory_status;
    }

    //入库   （type=1 采购入库，要减在途）
    public  function inItem($warehouse_id,$goods_id, $num,$ref_type=1)
    {
        $inventory = $this->findByGoodsid($warehouse_id,$goods_id);
        $wms_product_details = new WmsProductDetails();
        $good = $wms_product_details::getFullInfo($goods_id);
        if($num<0){
            return false;
        }
        if($inventory){//添加更新
            //增加库存
            $val = ['balance_count' => $inventory['balance_count']+$num ];
            if($ref_type==1 OR $ref_type==8){//采购入库，要减在途  // 调拨入库，要减在途
                $val['afloat_count'] = $inventory['afloat_count'] - $num;
            }
            $inventory_status =  \Yii::$app->db->createCommand()->update('wms_inventory', $val,"id = $inventory[id]")->execute();
            
        }else{//新增一条sku库存记录
            $val = ['goods_id' => $goods_id, 'balance_count' => $num, 'afloat_count' => 0, 'lock_count' => 0,'warehouse_id' => $warehouse_id ];
            $inventory_status =  \Yii::$app->db->createCommand()->insert('wms_inventory', $val)->execute();
        }
        //记录明细 
        $val = ['ref' => 'id'.date('YmdHis'), 'ref_type' => $ref_type, 'goods_id' => $goods_id, 'in_count' => $num ,'warehouse_id' => $warehouse_id ,'create_time' => date('Y-m-d H:i:s'),'balance_count' => $inventory['balance_count']+$num ];
        $wms_pick_round_detail =  \Yii::$app->db->createCommand()->insert('wms_inventory_detail', $val)->execute();
        return $wms_pick_round_detail;
    }

    


    //锁定库存
    public  function lockItem($warehouse_id,$goods_id, $num)
    {
        Yii::warning('匹配发货 锁定库存=> wh_id:'.$warehouse_id.'+goods_id:'.$goods_id.'+num:'.$num  , __METHOD__);
        $inventory = $this->findByGoodsid($warehouse_id,$goods_id);
        if($inventory['balance_count']-$inventory['lock_count']>$num){
            $val = ['lock_count' => $inventory['lock_count']+$num ];
            $inventory_status =  \Yii::$app->db->createCommand()->update('wms_inventory', $val,"id = $inventory[id]")->execute();
        }else{
            $inventory_status = -1; //库存不足
        } 
        Yii::warning('匹配发货 锁定库存返回:'.boolval($inventory_status)  , __METHOD__);
        return $inventory_status;
    }

    //出库  (type=3 销售出库,要减锁库存）
    public  function outItem($warehouse_id,$goods_id, $num,$ref_type=3)
    {
        $inventory = $this->findByGoodsid($warehouse_id,$goods_id);
        $wms_product_details = new WmsProductDetails();
        $good = $wms_product_details::getFullInfo($goods_id);     
        if($num<0){
            return false;
        }
        if($inventory){//添加更新
            //增加库存
            $val = [
                'balance_count' => $inventory['balance_count']-$num, 
            ];
            if($ref_type==3){//(type=3 销售出库,要减锁库存）
                $val['lock_count'] = $inventory['lock_count']-$num;
            }
            $inventory_status =  \Yii::$app->db->createCommand()->update('wms_inventory', $val,"id = $inventory[id]")->execute();
            
        }else{//没库存就无法减
            return false;
        }
        //记录明细 
        $val = ['ref' => 'id'.date('YmdHis'), 'ref_type' => $ref_type, 'goods_id' => $goods_id, 'out_count' => $num ,'warehouse_id' => $warehouse_id ,'create_time' => date('Y-m-d H:i:s'),'balance_count' => $inventory['balance_count']-$num ];
        $wms_pick_round_detail =  \Yii::$app->db->createCommand()->insert('wms_inventory_detail', $val)->execute();
        return $wms_pick_round_detail;
    }

    //'7'=>' 库存调拨-出库', 从a库出库到b库
    public  function outItemInside($warehouse_a,$warehouse_b,$goods_id, $num,$ref_type=7)
    {
        //a库减少库存
        $ret1 = $this->outItem($warehouse_a,$goods_id, $num,$ref_type);
        //b库增加在途
        $ret2 = $this->inAfloat($warehouse_b,$goods_id, $num );
        return $ret1;
    }

    //'8'=> '库存调拨-入库', 从a库入库到b库
    public  function inItemInside($warehouse_b,$goods_id, $num,$ref_type=8)
    {
        //b库增加库存，减少在途
        $ret1 = $this->inItem($warehouse_b,$goods_id, $num,$ref_type );
        return $ret1;
    }
    //

    //盘盈盘亏  对应 wms_ic_bill 表
    //TODO


    



    
    

    /**
     * 查询库存是否可用  TODO 要添加 warehouse_id
     * @param $sku
     * @param $num
     * @return boolean
     */
    public function checkInventory($num,$sku, $warehouse_id=1)
    {
        Yii::warning('查询库存是否满足， 需要数量=> num:'.$num.'+sku:'.$sku.'+wh_id:'.$warehouse_id  , __METHOD__);
        $wsm_product_details = new WmsProductDetails();
        $skus = $wsm_product_details::getGoodsId($sku); 
        $goods_id = $skus->id;
        $sql = "SELECT balance_count,lock_count FROM wms_inventory WHERE goods_id=:goods_id and warehouse_id=:warehouse_id";
        $stock = Yii::$app->db->createCommand($sql)->bindValue(':goods_id', $goods_id)->bindValue(':warehouse_id', $warehouse_id)->queryOne();

//        $sql2 = "SELECT SUM(A.number) AS num FROM wms_so_bill_detail AS A LEFT JOIN wms_so_bill AS B ON A.oid = B.order_no WHERE A.sku_code =:sku AND B.status IN (5)";
//        $qty = Yii::$app->db->createCommand($sql2)->bindValue(':sku', $sku)->queryOne();
        if ($stock['balance_count'] >= ($stock['lock_count']+$num)) {
            Yii::warning('查询库存是:可用'  , __METHOD__);
            return true;
        } else {
            Yii::warning('查询库存是:不可用，剩余可用库存：'.($stock['balance_count']-$stock['lock_count'])  , __METHOD__);
            return false;
        }
    }

    /**
     * SKU可用库存(不包含锁定数量）
     * 可用库存=实际库存-待发货-捡货中
     * @param $sku
     * @return int
     * @throws \yii\db\Exception
     */
    public function inventoryBySku($sku, $warehouse_id=1)
    {
        $wsm_product_details = new WmsProductDetails();
        $skus = $wsm_product_details::getGoodsId($sku);
        $goods_id = $skus->id;
        $sql = "SELECT balance_count,lock_count FROM wms_inventory WHERE goods_id=:goods_id and warehouse_id=:warehouse_id";
        $stock = Yii::$app->db->createCommand($sql)->bindValue(':goods_id', $goods_id)->bindValue(':warehouse_id', $warehouse_id)->queryOne();
//        $sql = "SELECT SUM(A.number) AS num FROM wms_so_bill_detail AS A LEFT JOIN wms_so_bill AS B ON A.oid = B.order_no WHERE A.sku_code=:sku AND B.status IN (5,6)";
//        $qty = Yii::$app->db->createCommand($sql)->bindValue(':sku', $sku)->queryOne();
        return $stock['balance_count'] - $stock['lock_count'];
    }



    



    public static function findByGoodsid($warehouse_id,$goods_id)
    {
        return static::findOne(['warehouse_id' => $warehouse_id,'goods_id' => $goods_id]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
}
