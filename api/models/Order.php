<?php

namespace api\models;
use api\models\ProductRelease;
use Yii;

/**
 * This is the model class for table "oms_order".
 *
 * @property string $id 订单id
 * @property string $activity_id 优惠活动表id
 * @property int $status 1.待确认，2已确认，3待采购，4已采购，5待发货，6捡货中，7已打包，8已发货，9已签收，10已取消，11已回款，12已退款，13拒签，14丢件，15问题件，16待转运，17缺货，18到货待确认，19缺货取消
 * @property string $product 产品名称
 * @property string $user_name 客户姓名
 * @property string $post_code 邮编
 * @property string $phone 手机号
 * @property string $email 邮箱
 * @property string $country 国家2字码
 * @property string $province 省份
 * @property string $city 市
 * @property string $area 区域
 * @property string $channel_type 货物类型 P普货 M敏感货
 * @property string $address 地址
 * @property string $id_card 证件图片路径
 * @property int $id_card_check 身份证和NPWP审核   0，未审核 1不通过，2通过
 * @property string $lc 货代
 * @property string $lc_number 物流单号
 * @property string $lc_foreign 落地配
 * @property string $total_price 订单总金额
 * @property string $price 单价
 * @property string $cost 采购成本（单位元）
 * @property int $number 商品数量
 * @property string $comment 工作人员备注
 * @property string $website 站点所属id
 * @property string $ads_user 投放人员
 * @property int $fail_times 订单配送失败次数
 * @property string $user_id 销售人员
 * @property int $payment_type 支付方式（1到付 暂定一种）
 * @property string $remark 订单备注
 * @property string $utm_source 推广渠道
 * @property string $utm_medium 推广小组
 * @property string $utm_ad 广告名
 * @property string $utm_content 优化师代号
 * @property string $utm_design 广告师代号
 * @property int $phone_check 手机验证结果0未验证 1 已验证
 * @property int $confirm 1确认2强烈要求取消 3想要取消又接受了4拒接5不接6说没定过7其它原因
 * @property int $money_status 0待结算，1已结算，2已退款
 * @property string $user_ip 用户下单ip
 * @property string $come_from 来源url
 * @property string $create_time 创建时间
 * @property string $shipping_time 送货时间段
 * @property string $create_by 创建人
 * @property int $is_del 是否删除（0否 1是）
 */
class Order extends \yii\db\ActiveRecord
{
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


    public function getProductRelease()
    {
        // 第一个参数为要关联的子表模型类名，
        // 第二个参数指定 通过子表的pid，关联主表的website字段
        return $this->hasOne(ProductRelease::className(), ['pid' => 'website']);
    }

    public $intStatus = ['没状态','待确认','已确认','待采购','已采购','待发货','捡货中','已打包','已发货','已签收','已取消','已回款','已退款','拒签','丢件','问题件','待转运','缺货','到货待确认','缺货取消','待处理','待通过'];

    public $statusInt = ['没状态'=>0,'待确认'=>1,'已确认'=>2,'待采购'=>3,'已采购'=>4,'待发货'=>5,'捡货中'=>6,'已打包'=>7,'已发货'=>8,'已签收'=>9,'已取消'=>10,'已回款'=>11,'已退款'=>12,'拒签'=>13,'丢件'=>14,'问题件'=>15,'待转运'=>16,'缺货'=>17,'到货待确认'=>18,'缺货取消'=>19,'待处理'=>20,'待通过'=>21];

    /**
     * 订单回调
     * @param $order_id
     */
    public function postBackForm()
    {
       $orderModel =new Order;
       $orderLogModel = new OrderLog();
       $order_id = Yii::$app->request->get('order_id');
       $status = Yii::$app->request->get('status');
       $data = $orderModel->findOne($order_id);
       $data->status = $status;
       $data->save();
        $orderLogModel->attributes =[
            'oid'=>$order_id,
            'action_user'=>2,
            'old_status'=>$data->status,
            'new status'=>$status,
            'remark'=>'wms远程回调修改订单状态',
            'action_ip'=>'127.0.0.1',
            'log_time'=>time(),
            'county'=>$data->country,
        ];
        $orderLogModel->save();
    }




    /*
     * 统计客户的签收率
     */
    public function NumberCount($phone)
    {
        $AllCount = self::find()->where(['phone' => $phone])->count();
        $RefuseCount = self::find()->where(['phone' => $phone, 'status' => 13])->count();
        return $RefuseCount / $AllCount;

    }

    /*
    * 查询是否存在未签收的订单
    */
    public function StatusCount($phone)
    {
        $AllCount = self::find()->where(['phone' => $phone])->andWhere(['not in', 'status', [9, 10, 11, 12, 13]])->count();
        return $AllCount;

    }

    /*
     * 查询是否有拒签
     */
    public function RefuseCount($phone)
    {
        $RefuseCount = self::find()->where(['phone' => $phone, 'status' => 13])->count();
        return $RefuseCount;
    }


    // //未签收订单处理
    // public function SaveOrders($model)
    // {
    //     $data = self::find()->where(['phone' => $model->phone])->andWhere(['not in', 'status', [9, 10, 11, 12, 13]])->all();
    //     $sku = [];
    //     foreach ($data as $v) {
    //         $sku[] = OrderGoods::find()->select('sku_code')->where(['oid' => $v->id])->one();
    //     }
    //     $classify = [];
    //     foreach ($sku as $v2) {
    //         $classify[] = substr($v2, 0, 1);
    //     }
    //     $GoodsData = Product::find()->where(['id' => $model->website])->one();
    //     $GoodsClassify = substr($GoodsData->spu, 0, 1);
    //     $orderModel = new Order();
    //     $logModel = new OrderLog();
    //     if (in_array($GoodsClassify, $classify)) {
    //         //存在同类产品
    //         $orderModel->attributes=[
    //             'status'=>10,
    //             'comment'=>'同类产品不接受新订单',
    //         ];
    //         $orderModel->save();
    //         $logModel->attributes =[
    //             'oid'=>$model->id,
    //             'action_user'=>'1',
    //             'old_status'=>'待确认',
    //             'new_status'=>'已取消',
    //             'remark' =>'同类产品不接受新订单',
    //             'action_ip'=>$_SERVER["REMOTE_ADDR"],
    //             'log_time' =>time(),
    //         ];
    //         $logModel->save();
    //         return false;
    //     } else {
    //         //不存在，检查异类订单数量
    //         if(count($classify)>=4){
    //             $orderModel->attributes=[
    //                 'status'=>10,
    //                 'comment'=>'同个用户异类产品最多只接收4个订单',
    //             ];
    //             $orderModel->save();
    //             $logModel->attributes =[
    //                 'oid'=>$model->id,
    //                 'action_user'=>'1',
    //                 'old_status'=>'待确认',
    //                 'new_status'=>'已取消',
    //                 'remark' =>'同个用户异类产品最多只接收4个订单',
    //                 'action_ip'=>$_SERVER["REMOTE_ADDR"],
    //                 'log_time' =>time(),
    //             ];
    //             $logModel->save();
    //             return false;
    //         }
    //         return true;

    //     }

    // }


    /**
     * 采购单取消减少采购单数量
     */
    public function detailStatus($id, $status)
    {
        if ($status == '已采购') {
            return true;
        } else {
            $order_item = OrderGoods::find()->where(['oid' => $id])->asArray()->all();
            foreach ($order_item as $value) {
                //查询出form的信息
                $data = RecordFrom::find()->where(['o_id' => $value['order_id']])->all();
                foreach ($data as $value2) {
                    $FormDates = Form::find()->where(['id' => $value2->f_id])->andWhere(['sku' => $value['sku']])->all();
                    foreach ($FormDates as $FormDate) {
                        if ($FormDate) {
                            $FormDate->number = $FormDate->number - $value['qty'];
                            if ($FormDate->save()) {
                                RecordFrom::deleteAll(['id' => $value2['id']]);
                            }
                        }
                    }
                }
            }

        }
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
        switch ($this->county) {
            case 'SG':
                $this->lc = '商壹';
                break;
            case 'TH':
//                $this->lc = 'TTI';
//                $this->lc = '商壹';
                $this->lc = '博佳图';
                break;
            case 'MY':
                $channelModel = new ChannelServices();
                $channel = $channelModel->getChannelByPostCode($this->county, $this->post_code);
                if ($channel) {
                    $this->lc = '商壹';
//                    $this->lc = 'K1';
                } else {
                    $this->lc = '1+1';
                }
                break;
            case 'ID':
                $product_id = Product::findOne($this->website);
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



    /**
     * 发送短信
     * @param $order_id
     * @param $mobile
     * @param $city
     */
    function sendMessage($order_id, $mobile, $city)
    {
        $code = rand(1000, 9999);
        $ch = curl_init();
        $apikey = "1a703871415268e7f2759ecd646fb186";
        $mobile = preg_replace('/[^\d]/i', '', $mobile);

        switch ($city) {
            case '臺灣':
            case 'TW':
                if (strpos($mobile, '886') === 0 && strlen($mobile) > 10) {
                    $mobile = substr($mobile, 3);
                }
                if (strlen($mobile) > 10) {
                    $mobile = substr($mobile, 0, 10);
                }
                $mobile = '+886' . $mobile;
                $text = "【WOWMALL】{$code}您的驗證碼. 您搶購的產品{$this->number}件共計{$this->total_price}新臺幣已經購買成功，訂單號{$order_id}，我們將盡快安排發貨，感謝您的支持！";
                break;
            case '香港':
            case 'HK':
                if (strpos($mobile, '852') === 0 && strlen($mobile) > 8) {
                    $mobile = substr($mobile, 3);
                }
                if (strlen($mobile) > 9) {
                    $mobile = substr($mobile, 0, 9);
                }
                $mobile = '+852' . $mobile;
                $text = "【WOWMALL】Your verification Code is {$code}. Hello,The product {$this->number} unit(s), {$this->total_price} has been succesfully purchased under order number {$this->id}, we shall arrange the delivery for you asap,thanks for your support!";
                break;
            case 'MY':
            case 'Malaysia':
                if (strpos($mobile, '60') === 0 && strlen($mobile) > 10) {
                    $mobile = substr($mobile, 2);
                }
                if (strlen($mobile) > 10) {
                    $mobile = substr($mobile, 0, 10);
                }
                $mobile = '+60' . $mobile;
                $text = "【WOWMALL】Your verification Code is {$code}. Hello,The product {$this->number} unit(s), {$this->total_price} has been succesfully purchased under order number {$this->id}, we shall arrange the delivery for you asap,thanks for your support!";
                break;
            case 'SG':
            case 'Singapore':
                if (strpos($mobile, '65') === 0 && strlen($mobile) > 8) {
                    $mobile = substr($mobile, 2);
                }
                if (strlen($mobile) > 8) {
                    $mobile = substr($mobile, 0, 8);
                }
                $mobile = '+65' . $mobile;
                $text = "【WOWMALL】Your verification Code is {$code}. Hello,The product {$this->number} unit(s), {$this->total_price} has been succesfully purchased under order number {$this->id}, we shall arrange the delivery for you asap,thanks for your support!";
                break;
            case 'TH':
                if (strpos($mobile, '66') === 0 && strlen($mobile) > 10) {
                    $mobile = substr($mobile, 2);
                }
                if (strlen($mobile) > 10) {
                    $mobile = substr($mobile, 0, 10);
                }
                $mobile = '+66' . $mobile;
                $text = "【WOWMALL】Your verification Code is {$code}. Hello,The product {$this->number} unit(s), {$this->total_price} has been succesfully purchased under order number {$this->id}, we shall arrange the delivery for you asap,thanks for your support!";
                break;
            case 'ID':
                if (strpos($mobile, '62') === 0 && strlen($mobile) > 11) {
                    $mobile = substr($mobile, 2);
                }
                if (strlen($mobile) > 12) {
                    $mobile = substr($mobile, 0, 12);
                }
                $mobile = '+62' . $mobile;
                $text = "【WOWMALL】kode verifikasi {$code}. Anda sudah sukses membeli {$this->number} unit dengan harga Rp{$this->total_price}. Nomor Pesanan: {$this->id}. Kami akan mengatur pengiriman secepatnya. estimasi waktu pengiriman 10-15 hari. Mohon sabar dan terima kasih.";
                break;
            case 'UAE':
                if (strpos($mobile, '00971') === 0 && strlen($mobile) > 10) {
                    $mobile = substr($mobile, 4);
                }
                if (strlen($mobile) > 10) {
                    $mobile = substr($mobile, 0, 10);
                }
                $mobile = '+971' . $mobile;
                $text = "【WOWMALL】Your verification Code is {$code}. Hello,The product {$this->number} unit(s), {$this->total_price} has been succesfully purchased under order number {$this->id}, we shall arrange the delivery for you asap,thanks for your support!";
                break;
            default:
        }

        if ($mobile) {
            //测试用
            if ($this->mobile == '13855142280') {
                $mobile = '13855142280';
            }
            // 发送短信
            $data = array('text' => $text, 'apikey' => $apikey, 'mobile' => $mobile);
            curl_setopt($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $json_data = curl_exec($ch);
            //如果curl发生错误，打印出错误
            if (curl_error($ch) != "") {
//            file_put_contents('./message_error.log', 'Curl error: ' . curl_error($ch));
            }
            //解析返回结果（json格式字符串）
            $array = json_decode($json_data, true);
            if ($array['code'] == 0) {
                $cookies = Yii::$app->response->cookies;
                $expire = time() + 15 * 60;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'sms_code',
                    'value' => $code,
                    'expire' => $expire
                ]));

                $expire = time() + 60;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'sms_send',
                    'value' => $code,
                    'expire' => $expire
                ]));

            } else {
                $this->mobile_check = 3;
                $this->save();
            }
        }

    }


    /**
     * AFF回调
     * @param $order_id
     */
    protected function postBackAff($order_id, $sku)
    {
        $utm = unserialize(Yii::$app->request->cookies->getValue('utm'));
        if($utm && $utm['utm_source'] == 'midastouch')
        {
            $create_date = date('Y-m-d H:i:s');
            $url = 'http://www.midastouch.cc/orders?offer_id='.urlencode($utm['utm_offid']).'&aff_id='.urlencode($utm['utm_aff']).'&sku='.urlencode($sku).'&order_id='.urlencode($order_id).'&order_date='.urlencode($create_date).'&status='.urlencode('待确认').'&sub_id='.urlencode($utm['utm_design']);
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_exec($ch);
//            $file_contents = curl_exec($ch);
//            file_put_contents('./aff.txt', $file_contents);
            curl_close($ch);
        }
        return true;
    }

    /**
     * POST BACK MV联盟
     * @return bool
     */
    protected function postBackMv()
    {
        $ip = Yii::$app->request->userIP;
        $clickid = Yii::$app->request->cookies->getValue('clickid', false);
        $uuid = Yii::$app->request->cookies->getValue('uuid', false);
        if ($clickid && $uuid) {
            $url = 'http://next.mobvista.com/install?mobvista_campuuid=' . urlencode($uuid) . '&mobvista_clickid=' . urlencode($clickid) . '&mobvista_ip=' . $ip;
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            //file_put_contents('./mv.txt', $file_contents);
            curl_close($ch);
        }
        return true;

    }

    /**
     * POST BACK outbrain
     * @return bool
     */
    protected function postBackOb()
    {
        $ob_click_id = Yii::$app->request->cookies->getValue('ObClickId', false);
        if ($ob_click_id) {
            $url = 'https://tr.outbrain.com/pixel?ob_click_id=.'.urlencode($ob_click_id).'&marketerId=00a1afb536e1be25f52269c435189a492b&name=conversion';
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            error_log(date('Y-m-d H:i:s')." ".$ob_click_id."\r\n",3,'./ob.txt');
            //file_put_contents('./ob.txt', $file_contents);
            curl_close($ch);
        }
        return true;

    }

    /**
     * POST BACK transaction
     * @return bool
     */
    protected function postBackTa()
    {
        $transaction_id = Yii::$app->request->cookies->getValue('transactionId', false);
        $affiliate_id = Yii::$app->request->cookies->getValue('affiliateId', false);
        if ($transaction_id||$affiliate_id) {
            $url = 'http://track.miadx.net/aff_lsr?transaction_id='.urlencode($transaction_id).'&affiliate_id='.urlencode($affiliate_id);
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            file_put_contents('./ta.txt', $file_contents);
            curl_close($ch);
        }
        return true;

    }

    /**
     * 产品对应品牌oms_brand 一对多
     */
    public function getOrderGoods()
    {
        return $this->hasMany(OrderGoods::className(), ['oid' => 'id']);
    }


}
