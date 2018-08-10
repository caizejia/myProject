<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_stocks".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $goods_id
 * @property integer $qty
 * @property string $create_date
 * @property string $expired_day
 * @property integer $status
 * @property string $fee
 * @property string $country
 * @property string $sales
 * @property string $track_company
 * @property string $track_number
 * @property integer $new_order_id
 * @property string $destroy_time
 * @property string $reservoir_area
 * @property string $location
 * @property string $print_time
 * @property string $outbound_time
 */
class WmsStocks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_stocks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'qty', 'status', 'new_order_id'], 'integer'],
            [['goods_id', 'qty', 'create_date', 'expired_day', 'status', 'country', 'sales', 'track_company', 'track_number'], 'required'],
            [['create_date', 'expired_day', 'destroy_time', 'print_time', 'outbound_time'], 'safe'],
            [['fee'], 'number'],
            [['country', 'sales', 'track_number'], 'string', 'max' => 50],
            [['track_company'], 'string', 'max' => 200],
            [['reservoir_area', 'location'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'goods_id' => 'Goods ID',
            'qty' => 'Qty',
            'create_date' => 'Create Date',
            'expired_day' => 'Expired Day',
            'status' => 'Status',
            'fee' => 'Fee',
            'country' => 'Country',
            'sales' => 'Sales',
            'track_company' => 'Track Company',
            'track_number' => 'Track Number',
            'new_order_id' => 'New Order ID',
            'destroy_time' => 'Destroy Time',
            'reservoir_area' => 'Reservoir Area',
            'location' => 'Location',
            'print_time' => 'Print Time',
            'outbound_time' => 'Outbound Time',
        ];
    }


    //定义api返回字段
    public function fields()
    {
      

        $details = new WmsProductDetails();
        return [ 
            'country', 
            'create_date', 
            'destroy_time',  
            'expired_day',  
            'fee',  
            'goods_id',  
            'id',  
            'location',  
            'new_order_id',  
            'order_id',  
            'outbound_time',  
            'print_time',  
            'qty',  
            'reservoir_area',  
            'sales',  
            'sku',  
            'status',  
            'track_company',  
            'track_number',   

            'sku_pic'=> function ($model) {
                $info = WmsProduct::getSkuInfo($model->sku);
                $img = json_decode($info['img_list'])[0];
                return $img ;
            }, 
            'sku_name'=> function ($model) {
                $info = WmsProduct::getSkuInfo($model->sku);
                return $info['name'] ;
            }, 
            
        ];
    }
}
