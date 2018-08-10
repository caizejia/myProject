<?php

namespace common\models;

use Yii;
use common\models\WmsSoBillDetail;
use common\models\WmsChannelServices;
/**
 * This is the model class for table "wms_so_bill".
 *
 * @property integer $id
 * @property string $order_no
 * @property integer $activity_id
 * @property integer $status
 * @property string $user_name
 * @property string $phone
 * @property string $email
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $total_price
 * @property string $cost
 * @property integer $number
 * @property integer $from_ad
 * @property integer $payment_type
 * @property string $remark
 * @property integer $user_ip
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $create_by
 * @property integer $update_by
 * @property integer $is_del
 * @property integer $supplier_id
 * @property integer $track_type
 * @property integer $track_num
 * @property integer $memo
 */
class WmsSoBill extends \yii\db\ActiveRecord
{

    public $statusArray = [
        '1' => '待确认',
        '2' => '已确认',
        '3' => '待采购',
        '4' => '已采购',
        '5' => '待发货',
        '6' => '捡货中',
        '7' => '已打包',
        '8' => '已发货',
        '9' => '已签收',
        '10' => '已取消',
        '11' => '已回款',
        '12' => '已退款',
        '13' => '拒签',
        '14' => '丢件',
        '15' => '问题件',
        '16'=>'待转运',
        '17' => '缺货',
        '18'=>'到货待确认',
        '19' => '缺货取消',
        '20' => '待处理',
        '21' => '待通过',
    ];

 

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'number', 'website', 'ads_user', 'user_id', 'create_time', 'phone_check', 'create_by', 'status', 'confirm', 'fail_times', 'id_card_check', 'is_del', 'money_status'], 'integer'],
            [['user_name', 'phone', 'country', 'address', 'total_price', 'number', 'website'], 'required'],
            [['total_price', 'price', 'cost'], 'number'],
            [['come_from'], 'string'],
            [['product', 'user_name', 'email', 'address', 'lc', 'lc_number', 'lc_foreign', 'comment', 'remark', 'utm_source', 'utm_medium', 'utm_ad', 'utm_content', 'utm_design'], 'string', 'max' => 255],
            [['post_code', 'shipping_time', 'payment_type'], 'string', 'max' => 50],
            [['phone', 'user_ip'], 'string', 'max' => 20],
            [['country'], 'string', 'max' => 2],
            [['province', 'city'], 'string', 'max' => 64],
            [['area'], 'string', 'max' => 128],
            [['channel_type'], 'string', 'max' => 1],
            [['id_card'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activity_id' => 'Activity ID',
            'status' => 'Status',
            'product' => 'Product',
            'user_name' => 'User Name',
            'post_code' => 'Post Code',
            'phone' => 'Phone',
            'email' => 'Email',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'channel_type' => 'Channel Type',
            'address' => 'Address',
            'id_card' => 'Id Card',
            'id_card_check' => 'Id Card Check',
            'lc' => 'Lc',
            'lc_number' => 'Lc Number',
            'lc_foreign' => 'Lc Foreign',
            'total_price' => 'Total Price',
            'price' => 'Price',
            'cost' => 'Cost',
            'number' => 'Number',
            'comment' => 'Comment',
            'website' => 'Website',
            'ads_user' => 'Ads User',
            'fail_times' => 'Fail Times',
            'user_id' => 'User ID',
            'payment_type' => 'Payment Type',
            'remark' => 'Remark',
            'utm_source' => 'Utm Source',
            'utm_medium' => 'Utm Medium',
            'utm_ad' => 'Utm Ad',
            'utm_content' => 'Utm Content',
            'utm_design' => 'Utm Design',
            'phone_check' => 'Phone Check',
            'confirm' => 'Confirm',
            'money_status' => 'Money Status',
            'user_ip' => 'User Ip',
            'come_from' => 'Come From',
            'create_time' => 'Create Time',
            'shipping_time' => 'Shipping Time',
            'create_by' => 'Create By',
            'is_del' => 'Is Del',
        ];
    }


     


    /*
     * 命令行定时任务匹配转运
     */
    public function updateStock($order){
        $items = Yii::$app->db->createCommand("SELECT number,sku_code FROM oms_order_goods WHERE oid = '{$order['id']}' ORDER BY sku_code ASC")->queryAll();

        $item_md5 = '';
        foreach($items as $item) {
            Yii::warning('匹配转运  订单详情:'.json_encode($item) , __METHOD__);
            $item_md5 .= strtoupper(trim($item['sku_code'])) . '=' . $item['number'] .'&';
        }
        $item_md5 = md5($item_md5);
        Yii::warning('匹配转运  订单详情md5:'.$item_md5 , __METHOD__);

        $country = $order['country'];
        $data = Yii::$app->db->createCommand("SELECT B.number,B.sku_code,B.oid,A.id FROM wms_stocks AS A LEFT JOIN oms_order_goods AS B ON A.order_id = B.oid WHERE A.country = '{$country}' AND A.status = '0' ORDER BY B.sku_code ASC")->queryAll();

        $stocks = array();
        foreach($data as $item) {
            $stocks[$item['oid']][] = ['sku_code' => $item['sku_code'], 'number' => $item['number'], 'id' => $item['id']];
        }
        Yii::warning('匹配转运  转运单详情 :'.json_encode($stocks) , __METHOD__);
        $data2 = '';
        foreach($stocks as $stock)
        {
            $md5 = '';
            foreach($stock as $v)
            {
                $md5 .= strtoupper(trim($v['sku_code'])) . '=' . $v['number'] . '&';
            }
            $md5 = md5($md5);
            Yii::warning('匹配转运  转运单详情md5:'.$md5 , __METHOD__);
            if($item_md5 == $md5)
            {
                $data2 = $stock[0];
                break;
            }
        }

        if ($data2){
            Yii::warning('匹配转运  转运单 匹配！！！ 设置新订单号：'.$order['id']  , __METHOD__);
            //匹配转运，设置新订单号
            Yii::$app->db->createCommand()->update('wms_stocks', [
                'status' => 1,
                'new_order_id' => $order['id'],
            ], "id = '{$data2['id']}'")->execute();

            Yii::$app->db->createCommand()->insert("wms_transport_id",[
                'new_order_id' => $order['id'],
                'old_order_id' => (Yii::$app->db->createCommand("select b.id from wms_stocks as s LEFT JOIN oms_order as b on s.order_id = b.id where s.id = '{$data2['id']}'")->queryOne())['id'],
            ])->execute();

            //订单转为待转运 status=16
            $this->setOrderStatus($order['id'],$order['status'],$after_status=16,$remark='系统自动定时任务匹配转运',$user_id=0); 
            Yii::warning('匹配转运    设置订单状态：待转运'  , __METHOD__);
        }else{
            Yii::warning('匹配转运  不匹配'  , __METHOD__);
        }
    }


    /**
     * 订单回调
     * @param $order_id
     */
    protected function postBackStatus($order_id, $status)
    {
        return true;
        $url = 'http://***.***.***/****?order_id='.$order_id.'status='.$status;  //OMS订单回调地址
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        
        return $file_contents;
    }

    // 这里是密集启动后台任务，匹配库存和订单，尽快把订单匹配发货
    // 操作步骤，匹配有货的库存的订单，修改订单状态为待发货，锁定库存
    public function matchInventory($order){
        $items = Yii::$app->db->createCommand("SELECT number,sku_code FROM oms_order_goods WHERE oid = '{$order['id']}' ORDER BY sku_code ASC")->queryAll();
        $wms_inventory = new WmsInventory1();
        $wms_product_details = new WmsProductDetails();
        $res = [];
        Yii::warning('匹配发货 正在获取订单详情:'.json_encode($items) , __METHOD__);
        foreach ($items as $item){
            $res[] = $a = $wms_inventory->checkInventory($item['number'],$item['sku_code']);
            if($a){
                Yii::warning('匹配发货  商品足够库存，已匹配:'.$item['sku_code'], __METHOD__);
            } 
        }
        if(in_array(false,$res)){
            Yii::warning('匹配发货  不完全匹配！！！', __METHOD__);
            return false;
        }
        //全匹配的状态
        Yii::warning('匹配发货 全匹配的状态,开始锁定库存！！'  , __METHOD__);
        foreach ($items as $item){
            $goods_id = ($wms_product_details::getGoodsId($item['sku_code']))->id;
            $warehouse_id = '1'; //TODO 这里要设置默认出库库存
            //锁定库存
            $result = $wms_inventory->lockItem($warehouse_id,$goods_id, $item['number']);
 
        }


        //订单转为待发货 status=5
        return $this->setOrderStatus($order['id'],$order['status'],$after_status=5,$remark='系统自动匹配仓库库存',$user_id=0);  //修改为待发货
    }

    //设置 订单状态 并记录日志， 返回通知OMS并记录反馈 , 记录订单时间节点
    public  function setOrderStatus($order_id,$before_status,$after_status,$remark='系统修改状态',$user_id=0)
    {
        //1,设置 订单状态
        Yii::$app->db->createCommand()->update('oms_order', [
            'status' => $after_status,
        ], "id = '{$order_id}'")->execute();
        //2,并记录仓库日志
        Yii::$app->db->createCommand()->insert("wms_order_log",[
            'order_id' => $order_id,
            'fields' => 'status',
            'before' => $before_status,
            'after' => $after_status,
            'remark' => $remark,
            'user_id' => $user_id,
        ])->execute();

        //3 通知oms
        /*$feedback = $this->postBackStatus($order_id, $after_status);
        if(empty($feedback)){
            //重试1次
            $feedback = $this->postBackStatus($order_id, $after_status);
            //记录回调错误日志
            if(!$feedback){
                Yii::$app->db->createCommand()->insert("wms_callback",[
                    'order_id' => $order_id,
                    'status' => $after_status,
                ])->execute();
            }
        }*/

        //4,记录订单时间节点
        if($time = Yii::$app->db->createCommand("select id from wms_orders_time where order_id = '{$order_id}'")->queryOne()){
            Yii::$app->db->createCommand()->update('wms_orders_time', [
                "s{$after_status}" => date('Y-m-d H:i:s'),
            ], "id = '{$time['id']}'")->execute();
        }else{
            Yii::$app->db->createCommand()->insert("wms_orders_time",[
                'order_id' => $order_id,
                "s{$after_status}" => date('Y-m-d H:i:s'),
            ])->execute();
        }
        return true;
    }


    /**
     * 自动调整发货渠道
     *
     * 马来  商壹只能走DHL   先筛选订单邮编选DHL  能走就走DHL 不能走就走1+1
     * 新加坡 全走1+1
     * 泰国  敏感货1+1   普货 商壹
     * @return Boolean
     */
    public function updateTrackName()
    {
        switch ($this->country) {
            case 'SG':
                $this->lc = '商壹';
                break;
            case 'TH':
//                $this->lc = 'TTI';
//                $this->lc = '商壹';
                $this->lc = '博佳图';
                break;
            case 'MY':
                $channelModel = new WmsChannelServices();
                $channel = $channelModel->getChannelByPostCode($this->county, $this->post_code);
                if ($channel) {
                    $this->lc = '商壹';
//                    $this->lc = 'K1';
                } else {
                    $this->lc = '1+1';
                }
                break;
            case 'ID':
                $product_id = Products::findOne($this->website);///TODO 未完成
                if(trim($product_id->id_link)){
                    $this->lc = 'AFL';
                }else{
                    $this->lc = '云路';
                }
                break;
            case 'UAE':
                $this->lc = 'imile';
                break;
            case '臺灣':
                $this->lc = '易速配';
                break;
            case 'LKA':
                $this->lc = '商壹';
                break;
            case 'HK':
//                $this->lc = '顺丰';
            default:
                $this->lc = '森鸿';
        }
        return $this->save();
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function getDetails()
    {
        return $this->hasMany(WmsSoBillDetail::className(), ['oid' => 'id']);
    }


}
