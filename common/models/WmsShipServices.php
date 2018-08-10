<?php

namespace common\models;

use Yii;
use common\models\WmsSoBill; 
use common\models\WmsSoBillDetail; 
use common\models\WmsOrderPackageWz;
use common\models\WmsProduct;
use common\models\WmsChannelServices;
use common\models\Yunlu;
use common\models\NinjaVan;
use common\models\WmsCdsKey;
use common\models\WmsTwTrackNumber;
/**
 * 生成物流跟踪号功能 ,  尽量和物流申请的相关内容都写在这里，和订单分开
 *
 * @property string $id
 * @property string $country
 * @property string $post_code
 * @property string $channel
 */
class WmsShipServices extends \yii\db\ActiveRecord
{
    public $base_pdf_url = 'http://api.orkowms.me/site';

    

   //申请物流单号
    public function build($order_id){
        $order = WmsSoBill::findOne($order_id);
        $order_detail = WmsSoBillDetail::find()->andWhere(['=', 'order_id', $order->id])->all();
        $order_package = WmsOrderPackageWz::find()->andWhere(['=','order_id',$order['id']])->one();
        $product = WmsProduct::findOne($order->website);
        //原来的参数
        $old_status = $order->status;
        $old_lc = $order->lc;
        $old_lc_number = $order->lc_number;
        $order->status = 7;//'已打包' 
        $shipping_ok = false;
        //返回的参数
        $return = []; //返回值
        $return['code'] = 200;
        $return['msg'] = '保存成功, 货代：';
        $return['lc_number'] = $order->lc_number;
        $return['pdf_url'] = '';

 
        switch ($order->lc) {
            case '顺丰': 
            try {
                $client = new \SoapClient("https://bsp-oisp.sf-express.com/bsp-oisp/ws/expressService?wsdl");
                        //            var_dump($client->__getFunctions());die;
                $request = '<Request service="OrderService" lang="zh-CN">
                <Head>SZAKXXKJ,UR75gdXgn9HShtgSnkipmpsNo79IzhxS</Head>
                <Body>
                <Order
                orderid="'.$order->id.'"
                j_company="Shenzhen Orko Info and technology Co., Ltd"
                j_contact="Kevin"
                j_tel="985625083@qq.com"
                j_mobile="985625083@qq.com"
                j_shippercode="CN"
                j_country="中国"
                j_province="广东省"
                j_city="深圳市"
                j_country="宝安区"
                j_address="Building, Room 535, No. 288 Xixiang Dadao,, Baoan, Shenzhen, China"
                j_post_code="518000"
                d_company="'.$order->name.'"
                d_contact="'.$order->name.'"
                d_tel="'.$order->mobile.'"
                d_deliverycode="HK"
                d_country="HK"
                d_province="Hong Kong"
                d_city="Hong Kong"
                d_country="Hong Kong"
                custid="7550057046"
                pay_method="1"
                express_type="1"
                is_gen_bill_no="1"
                d_address="'.$order->address.'"
                parcel_quantity="1"
                cargo_total_weight="'.sprintf('%.3f',$order_package->weight).'"
                declared_value="'.sprintf('%.3f',$order->price - $order->prepayment_amount).'"
                declared_value_currency="HKD">
                <Cargo
                name="'.$product->declaration_cname.'"
                count="1"
                unit="个"
                weight="'.sprintf('%.3f',$order_package->weight).'"
                amount="'.sprintf('%.3f',$order->price - $order->prepayment_amount).'"
                currency="HKD"
                source_area="HK">
                </Cargo>
                <AddedService
                name="COD"
                value="'. sprintf('%.3f',$order->price - $order->prepayment_amount) .'" >
                </AddedService>
                </Order>
                </Body>
                </Request>';
                $return = $client->sfexpressService(['arg0'=>$request]);
                $response = $return->return;
                $response = simplexml_load_string($response);
                if($response->Head == 'OK'){
                    $lc_number_r = $response->Body;
                    $lc_number_r = $lc_number_r->OrderResponse;
                    $lc_number_r = ($lc_number_r->attributes());
                    $lc_number_r = $lc_number_r->mailno;
                    $lc_number_r = json_decode(json_encode($lc_number_r),true);
                    $lc_number_r = $lc_number_r[0];
                    $order->lc_number = $lc_number_r;
                    $order->save();
                    $pdf = $this->curl_file_get_contents($this->$base_pdf_url.'/sf-single-plane?id=' . $order->id);
                    file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);

                    $return['code'] = 200;
                    $return['msg'] = '保存成功, 货代：顺丰';
                    $return['lc_number'] = $order->lc_number;
                    $return['pdf_url'] = $this->$base_pdf_url.'/sf-single-plane?id=' . $order->id;
                    $shipping_ok = true;
                }else{
                    $return['code'] = 500;
                    $return['msg'] = $response->ERROR; 
                }
            } catch (\SoapFault $e) {
                $return['code'] = 500;
                $return['msg'] = '系统错误：' . $e; 
            }
            break;





            case '皇家':
            $customerid = '93852';
            $secretkey = '5d50af88-f8dc-4fb0-ad38-aa39ee522dfa93852';

            $price = intval($order->price / $order->qty * $order->converter[$order->country]);
            $district = $order->country == 'SG' ? 'Singapore' : $order->district;

            $ship_order = [
                'Type' => 2,
                'INorOut' => 0,
                'CsRefNo' => $order->id,
                'CustomerId' => $customerid,
                'ChannelId' => $order->pfcChannelCode[$order->country],
                'TrackingNo' => '',
                'ShipToName' => $order->name,
                'ShipToPhoneNumber' => $order->mobile,
                'ShipToCountry' => $order->country,
                'ShipToState' => $order->city,
                'ShipToCity' => $district,
                'ShipToAdress1' => $order->address,
                'ShipToZipCode' => $order->post_code,

                'OrderStatus' => 1,
                'Remark' => $order->comment,
                'BatteryFlag' => 1,
                'CODFee' => $order->price,
                'Products' => [
                    [
                        'SKU' => $product->sku,
                        'EnName' => $product->declaration_ename,
                        'CnName' => $product->declaration_cname,
                        'MaterialQuantity' => $order->qty,
                        'Price' => $price,
                        'Weight' => $order->package->weight
                    ]
                ]
            ];
            $data_json = json_encode($ship_order);
            $url = 'http://www.pfcexpress.com/webservice/v2/CreateShipment.aspx';

            $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 抓取结果直接返回（如果为0，则直接输出内容到页面）
                curl_setopt($ch, CURLOPT_HEADER, 0);// 不需要页面的HTTP头
                curl_setopt($ch, CURLOPT_URL, $url);  // 设置要抓取的页面地址
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                curl_setopt($ch, CURLOPT_URL, $url);  // 设置要抓取的页面地址
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $secretkey, 'Content-Type: application/json', 'Content-Length:' . strlen($data_json)]);

                $trackOrder = curl_exec($ch);
                $trackOrder = $order->createPfcOrder();
                if ($trackOrder->status != '200') {
                    $return['code'] = 500;
                    $return['msg'] = $trackOrder->msg;
                    $shipping_ok = false;
                } else {
                    $order->shipping_date = date('Y-m-d H:i:s');
                    $order->lc_number = $trackOrder->data->TrackingNo;
                    $order->save();
                    $return['code'] = 200;
                    $return['msg'] = $trackOrder->msg;
                    $return['lc_number'] = $order->lc_number;
                    $return['pdf_url'] = $this->$base_pdf_url.'/sf-single-plane?id=' . $order->id;
                    $shipping_ok = true;
                } 
            break;






            case '商壹':
                //2018年2月9号，商壹泰国无法走货改为博佳图
                if (false && $order->country == 'TH') {
                    $order->lc = '博佳图';
                    $order->save();
                    $return['code'] = 200;
                    $return['msg'] = '保存成功, 货代：博佳图'; 
                } else {
                    $key = '34947aeefa6d364dba91ffb48c032202';
                    $userid = '610212';
                    $password = '1263055817';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 抓取结果直接返回（如果为0，则直接输出内容到页面）
                    curl_setopt($ch, CURLOPT_HEADER, 0);// 不需要页面的HTTP头
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                    //        $url = 'http://www.com1express.net/api/getAuth.html?key='.$key.'&userid='.$userid.'&password='.$password;
                    //        curl_setopt($ch, CURLOPT_URL, $url);
                    //        $output = curl_exec($ch);
                    //        $auth = json_decode($output);
 
                    if ($order->country == 'MY') {
                        $country = $this->getMyCode($order->post_code);
                    } else {
                        $country = $order->country;
                    }

                    $channel_id = $this->getChannelId($order->country,$order->channel_type,$order->post_code);
                    $unit_price = intval($order->price / $order->qty * $order->converter[$order->country]);
                    $district = $order->country == 'SG' ? 'Singapore' : $order->district;
                    $address = $order->address;
                    if ($order->area) {
                        $address .= ', ' . $order->area;
                    }

                    $ship_data = [
                        'resultid' => 100013,
                        'userid' => $userid,
                        'country' => $country,
                        'channel_id' => $channel_id,
                        'ordernum' => $order->id,
                        'count' => 1,
                        'weight' => $weight,
                        'remark' => '',
                        'cod' => $order->price,
                        'currencytype' => $this->currencyType[$order->country],
                        'items' => [
                            [
                                'cname' => $product->declaration_ename,//$product->declaration_cname
                                'name' => $product->declaration_ename,
                                'number' => $order->qty,
                                'unit_price' => $unit_price
                            ]
                        ],
                        'consignee' => [
                            'name' => $order->name,
                            'company' => $order->name,
                            'phone' => $order->mobile,
                            'address' => $address,
                            'postcode' => trim($order->post_code),
                            'email' => $order->email,
                            'state' => $order->city,
                            'city' => $district,
                        ],

                    ];
                    if ($order_package->length) {
                        $ship_data['size'] = [
                            [
                                'long' => $order_package->length,
                                'width' => $order_package->width,
                                'heigth' => $order_package->height,
                                'count' => 1
                            ]
                        ];
                    }
                    $url = 'http://www.com1express.net/api/CreateOrder.html?key=' . $key;
                    $data_json = json_encode($ship_data);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length:' . strlen($data_json)]);
                    $output = curl_exec($ch);
                    $trackOrder = json_decode($output);
                     
                    if ($trackOrder->status == 'fail') {
                        $return['code'] = 500;
                        $return['msg'] = $trackOrder->msg;
                        $return['lc_number'] = '';
                        $return['pdf_url'] = '';
                        $shipping_ok = false; 
                    } else {
                        $channel_id = $this->getChannelId($order->country,$order->channel_type,$order->post_code);
                        $order->shipping_date = date('Y-m-d H:i:s');
                        $order->lc_number = $trackOrder->ordernum;
                        $order->save();
                        $order_num = $trackOrder->ordernum;
                        if($order->country == 'SG'){
                            $pdf_url = 'http://www.com1express.net/api/getLabel.html?key=34947aeefa6d364dba91ffb48c032202&onlineflag=true&type=10*18&ordernum=' . $trackOrder->ordernum;
                        }else{
                            if (in_array($channel_id, [371, 370])) {
                                $pdf_url = 'http://www.com1express.net/api/getLabel.html?key=34947aeefa6d364dba91ffb48c032202&onlineflag=true&type=dhl&ordernum=' . $trackOrder->ordernum;
                            } else {
                                $pdf_url = 'http://www.com1express.net/api/getLabel.html?key=34947aeefa6d364dba91ffb48c032202&onlineflag=true&type=10*18&ordernum=' . $trackOrder->ordernum;
                            }
                        }
                        $pdf = $this->curl_file_get_contents($pdf_url);
                        file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $trackOrder->ordernum . '.pdf', $pdf);

                        $return['code'] = 200;
                        $return['msg'] = '保存成功';
                        $return['lc_number'] = $order_num;
                        $return['pdf_url'] = $pdf_url;
                        $shipping_ok = true;
                    }
                }
            break;




            case '1+1':
                $order->save();
                $return['code'] = 200;
                $return['msg'] = '保存成功';
                $shipping_ok = true;
            break;



            case '东丰物流':
                $order->save();
                $return['code'] = 200;
                $return['msg'] = '保存成功';
                $shipping_ok = true;
            break;


            case '云路':
                $yl = new Yunlu();
                $yl_data = $yl->orderCreate($order->id);
                if ($yl_data->responseitems[0]->mailno) {
                    $order->lc_number = $yl_data->responseitems[0]->mailno;
                    $order->save();
                    sleep(2);
                    $yl_data = $yl->routePrint($order->id);
                    $pdf = $this->curl_file_get_contents($yl_data->responseitems[0]->rotaprinturl);
                    file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);
                    $return['code'] = 200;
                    $return['msg'] = '保存成功';
                    $return['lc_number'] = $order->lc_number;
                    $return['pdf_url'] = $yl_data->responseitems[0]->rotaprinturl;
                    $shipping_ok = true;
                } else {
                    $return['code'] = 500;
                    $return['msg'] =  '云路系统错误：' . $yl_data->responseitems[0]->reason; 
                }

            break;





            case 'TTI':
                $order->lc_number = 'TTI' . $order->id;
                $order->save();
                if($order->country == 'TH')
                {
                    $pdf = $this->curl_file_get_contents($this->$base_pdf_url.'/th-single-plane?id=' . $order->id);
                    file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);

                    $return['code'] = 200;
                    $return['msg'] = '保存成功, 货代：TTI';
                    $return['lc_number'] = $order->lc_number;
                    $return['pdf_url'] = $this->$base_pdf_url.'/th-single-plane?id=' . $order->id;
                }
                $shipping_ok = true;
            break;





            case 'TJM':
                if($order->country == 'MY')
                {
                    $channel_id = $this->getChannelId($order->country,$order->channel_type,$order->post_code);
                    if (!in_array($channel_id, [371, 370])) {
                        $order->lc = '商壹';
                        $order->status = 6;//'捡货中';
                        $order->save();
                        $return['code'] = 500;
                        $return['msg'] = 'DHL不支持该地区, 货代已更改为商一，请重新称重'; 
                    }
                }
                $order->lc_number = 'TJM' . $order->id;
                $order->save();

                $pdf = $this->curl_file_get_contents($this->$base_pdf_url.'/single-plane?id=' . $order->id);
                file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);

                $return['code'] = 200;
                $return['msg'] = '保存成功, 货代： '.$order->lc;
                $return['lc_number'] = $order->lc_number;
                $return['pdf_url'] = $this->$base_pdf_url.'/single-plane?id=' . $order->id;
                $shipping_ok = true;
            break;





            case 'K1':
            if($order->country == 'MY')
            {
                $channel_id = $this->getChannelId($order->country,$order->channel_type,$order->post_code);
                if (!in_array($channel_id, [371, 370])) {
                    $order->lc = '商壹';
                    $order->status = '捡货中';
                    $order->save();
                    $return['code'] = 500;
                    $return['msg'] = 'DHL不支持该地区, 货代已更改为商一，请重新称重';  
                }else{
                    list($msec, $sec) = explode(' ', microtime());
                    $msectime = floor(($sec + $msec) * 1000);
                    switch ($order->country){
                        case 'TH':
                        $country = 'Thailand';
                        $price = $order->price - $order->prepayment_amount;
                        break;
                        case 'MY':
                        $country = 'Malaysia';
                        $price = $order->price - $order->prepayment_amount;
                        break;
                        case 'VN':
                        $country = 'Vietnam';
                        $price = sprintf('%.0f',($order->price - $order->prepayment_amount) / 500 ) * 500;
                        break;
                        default:
                        $country = '';
                        $price = $order->price - $order->prepayment_amount;
                        break;
                    }
    
                    $gcount = 0;
                    foreach ($order_detail as $order_item) {
                            $orderDetails[] = [
                                "sku" => $order_item->sku,
                                "productname" => $product->declaration_cname,
                                "productenname" => $product->declaration_ename,
                                "price" => sprintf('%.0f', $price / count($order_detail)).'',
                                "gcount" => $order_item->qty ,
                                "isCharge" => "0",
                                "hsCode"=>"7113209090",
                                "currency" => $order->currencyType[$order->country],
                                "unit" => sprintf('%.0f', ($price / count($order_detail)) / $order_item->qty) .'',
                            ];
                            $gcount += $order_item->qty;
                    }

                    $logisticsChannel = $order->country . '-DHL-COD-';
                    $logisticsChannel .= $product->product_type == '普货' ? 'P' : 'T';
                    $api_key = '5d03e6949ee2433c9ee5debc149f573e';
                    $api_secret = 'c48a0843f1448f690b93591c12f63513';
                    $business_code = 'A10063A';
                    $data1 = [
                            "api_key" => $api_key,
                            "country" => $country,
                            "isCharge" => "0",
                            "isCod" => "1",
                            "total" => $price ,
                            "saleNumber" => $order->id ,
                            "totalWeight" => ($order_package['weight']) * 1000 ,
                            "postCode" => $order->post_code,
                            "phone" => $order->mobile,
                            "nameto" => $order->name,
                            "addressto" => $order->address,
                            "cityto" => $order->district,
                            "provinceto" => $order->city,
                            "orderDetails" => $orderDetails,
                            "codValue" => $order->price - $order->prepayment_amount,
                            "logisticsChannel" => $logisticsChannel,     //物流渠道编码
                            "businessCode" => $business_code,   //商家编码
                            "gcount" => $gcount , //总个数
                            "isSensitiv" => $product->product_type == '普货' ? "1" : '2', // 是否敏感
                            "isLiquid " => "1",  //是否液体
                            "isPowder" => "1",  //是否是粉末
                            "isTaxation" => "1", // 是否免征税
                            "currency" => $order->currencyType[$order->country],
                            "countryCode" => $order->country,
                            "trackingType" => "DHL",
                            "volume" => $order_package['length'] . '*' . $order_package['width'] . '*' . $order_package['height'] . '*' . '1',
                                //"volume" => "3*3*3*1",
                            "isSaveTms" => "1",
                    ];
                    $data2 = json_encode($data1);
                    $validateCode = md5($msectime . $data2 . pack('H*', $api_secret));
                    $post_data = [
                            "validateCode" => $validateCode,
                            "requestId" => $msectime,
                            "data" => $data1
                    ];

                    $url = "http://api.kuajingyihao.com/v1/Tracking/getTrackingNumber";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
                    $output = curl_exec($ch);
                    curl_close($ch);
                    $output_arr = json_decode($output, true);
                    if($output_arr['labelResponse']['bd']['responseStatus']['code'] == 200) {
                        $order_id_k = $order->id;
                        $order->lc_number = $lc_number_k = $output_arr['labelResponse']['bd']['labels'][0]['deliveryConfirmationNo'];
                        $order->save();
                        file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.png', base64_decode($output_arr['labelResponse']['bd']['labels'][0]['content']));
                        /*  file_put_contents(Yii::$app->getBasePath() . '/web/upload/1.txt',json_encode($post_data).'////'.$output);*/
                        $pdf = $this->curl_file_get_contents($this->$base_pdf_url. '/single-plane-a?id=' . $order->id);
                        file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);
                        $return['code'] = 200;
                        $return['msg'] = '保存成功, 货代：K1 ' ;
                        $return['lc_number'] = $order->lc_number;
                        $return['pdf_url'] = $this->$base_pdf_url.'/single-plane?id=' . $order->id;
                        $shipping_ok = true;
                    }else{
                        if($output_arr['labelResponse']['bd']['labels'][0]['responseStatus']){
                            $return['code'] = 500;
                            $return['msg'] = $output_arr['labelResponse']['bd']['labels'][0]['responseStatus']['messageDetails'][0]['messageDetail'];
                            $return['lc_number'] = '';
                            $return['pdf_url'] = ''; 
                        }else{
                            $return['code'] = 500;
                            $return['msg'] = $output_arr['msg']; 
                        }
                        $shipping_ok = false;
                    }
                }
            }
            break;





            case 'CDS':  //cds 马来  DHL
            if($order->country == 'MY')
            {
                $channel_id = $this->getChannelId($order->country,$order->channel_type,$order->post_code);
                if (!in_array($channel_id, [371, 370])) {
                                $order->lc = '商壹';
                                $order->status = '捡货中';
                                $order->save();
                                $return['code'] = 500;
                                $return['msg'] = 'DHL不支持该地区, 货代已更改为商一，请重新称重'; 
                                
                }
            }


            $cdsModel = new WmsCdsKey();
            $cdsModel->attributes = [
                'order_id' => $order->id
            ];
            $cdsModel->save();
            $order->lc_number = 'KWT' . sprintf("%'.010d", $order->id);
            $order->save();

            $pdf = $this->curl_file_get_contents($this->$base_pdf_url.'/single-plane-th?id=' . $order->id);
            file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);

            $return['code'] = 200;
            $return['msg'] = '保存成功, 货代：CDS';
            $return['lc_number'] = $order->lc_number;
            $return['pdf_url'] = $this->$base_pdf_url.'/single-plane-th?id=' . $order->id;; 
            $shipping_ok = true;
            break;




            case '易速配':
            if($order->country == '臺灣')
            {
                $tb = Yii::$app->db->beginTransaction();
                try{
                    //台湾贷代
                    $twTrack = new WmsTwTrackNumber();
                    $tw_tracknumber = $twTrack->find()->where('order_id=0')->orderBy('id ASC')->one();
                    if($tw_tracknumber){
                        $get_tw_track_number = true;
                        do{
                            if(Yii::$app->db->createCommand("UPDATE tw_track_number SET order_id='{$order->id}' WHERE order_id=0 AND id={$tw_tracknumber->id}")->execute()){
                                $get_tw_track_number = false;
                            }else{
                                $tw_tracknumber = $twTrack->find()->where('order_id=0')->orderBy('id ASC')->one();
                            }
                        }while($get_tw_track_number);

                        $order->lc_number = $tw_tracknumber->track_number;
                        $order->save();
                        $tb->commit();

                        $pdf = $this->curl_file_get_contents($this->$base_pdf_url.'/tw-single-plane?id=' . $order->id);
                        file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);

                        $return['code'] = 200;
                        $return['msg'] = '保存成功, 货代：易速配';
                        $return['lc_number'] = $order->lc_number;
                        $return['pdf_url'] = $this->$base_pdf_url.'/tw-single-plane?id=' . $order->id;; 
                        $shipping_ok = true;
                    }else{
                        $return['code'] = 500;
                        $return['msg'] = '警告：运单号已用完，请联系物流部添加！';
                    }
                }catch (\Exception $e){
                    $tb->rollBack();
                }
            }

            if($order->country == 'HK'){
                $order->save();
                $return['code'] = 200;
                $return['msg'] = '保存成功, 货代：货代' . $order->lc;
                $shipping_ok = true;
            }
            break;




            case '汉邮':
                $order->save();
                $return['code'] = 200;
                $return['msg'] = '保存成功, 货代：汉邮'  ;
                $shipping_ok = true;
            break;



            case 'imile':
                $order->save();
                $return['code'] = 200;
                $return['msg'] = '保存成功, 货代：imile'  ; 
                $shipping_ok = true;
            break;




            case 'NinjaVan':
                $njv = new NinjaVan();
                $njv->set_country_code($order->country);
                $njv_data = $njv->orderCreate($order->id);
                if ($njv_data['tracking_number']) {
                    $order->lc_number = $njv_data['tracking_number'];
                    $order->save();
                    $njv_data = $njv->routePrint($order->id);
                    $pdf = $this->curl_file_get_contents($njv_data['print']);
                    file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);
                    $return['code'] = 200;
                    $return['msg'] = '保存成功, 货代：NinjaVan';
                    $return['lc_number'] = $order->lc_number;
                    $return['pdf_url'] =  $njv_data['print']; 
                    $shipping_ok = true;
                } else {
                    $return['code'] = 500;
                    $return['msg'] = 'NinjaVan系统错误：' . $njv_data['error'];
                }
                break;




            case '博佳图': 
            try {
                $client = new \SoapClient("http://120.79.190.37/default/svc/wsdl");
                $request = [
                    'appToken'=>'1cc41f708a9e1c76f5f22ac8ac6b12bb',
                    'appKey'=>'1cc41f708a9e1c76f5f22ac8ac6b12bbafc3d5c71d18862ae8c12b3ba92dd1ac',
                    'service'=>'createOrder',
                    'paramsJson'=>json_encode([
                        'reference_no'=> $order->id,
//                                        'shipper_hawbcode'=> '',  //系统单号
                        'shipping_method'=> 'PK0001',
                        'country_code'=> 'TH',
//                                        'extra_service'=> '',
                        'order_weight'=> $weight,
//                                        'shipping_method_no'=> '',
                        'order_pieces'=> 1,
//                                        'insurance_value'=> '',
//                                        'mail_cargo_type'=> '',
                        'length'=> 1,
                        'width'=> 1,
                        'height'=> 1,
                        'is_return'=> 1,
                        'sales_amount'=> $order->price - $order->prepayment_amount,
                        'sales_currency'=> 'THB',
                        'is_COD' => 'Y',
                        'Consignee'=> [
//                                            'consignee_company' => '',
                            'consignee_province' => $order->city,
                            'consignee_city' => $order->district,
                            'consignee_street' => $order->address . $order->district . $order->city,
//                                            'consignee_street2' => '',
//                                            'consignee_street3' => '',
                            'consignee_postcode' => $order->post_code,
                            'consignee_name' => $order->name,
//                                            'consignee_telephone' => $order->mobile,
                            'consignee_mobile' => $order->mobile,
                            'consignee_email' => $order->email,
//                                            'consignee_certificatetype' => '',
//                                            'consignee_certificatecod' => '',
//                                            'consignee_credentials_period' => '',
//                                            'buyer_id' => '',
//                                            'consignee_doorplate' => '',
//                                            'consignee_taxno' => '',
                        ],
                        'Shipper'=> [
//                                            'shipper_company' => '',
//                                            'shipper_countrycode' => '',
//                                            'shipper_province' => '',
//                                            'shipper_city' => '',
//                                            'shipper_street' => '',
//                                            'shipper_postcode' => '',
//                                            'shipper_areacode' => '',
//                                            'shipper_name' => '',
//                                            'shipper_telephone' => '',
//                                            'shipper_mobile' => '',
//                                            'shipper_email' => '',
//                                            'shipper_fax' => '',
//                                            'order_note' => '',
                        ],
                        'ItemArr'=> [[
                            'invoice_enname' => $product->declaration_ename,
                            'invoice_cnname' => $product->declaration_cname,
                            'invoice_weight' => $weight,
                            'invoice_quantity' => 1,
//                                            'unit_code' => '',
//                                            'invoice_unitcharge' => $product->declaration_price,
                            'invoice_unitcharge' => $order->price - $order->prepayment_amount,
                            'invoice_currencycode' => 'THB',
//                                            'hs_code' => '',
//                                            'invoice_note' => '',
//                                            'invoice_url' => '',
//                                            'sku' => '',
//                                            'box_number' => '',
                        ]],
                        'Volume'=> [[
//                                            'length' => '',
//                                            'width' => '',
//                                            'height' => '',
//                                            'child_number' => '',
//                                            'refer_number' => '',
                        ]],
                    ]),
                ];
                $return = $client->callService($request);
                $response = $return->response;
                $response = json_decode($response);
                if($response->ask == 'Success'){
                    $order->lc_number = $response->order_code;
                    $order->save();
                    $pdf = $this->curl_file_get_contents($this->$base_pdf_url.'/th-single-plane-bjt?id=' . $order->id);
                    file_put_contents(Yii::$app->getBasePath() . '/web/pdf/' . $order->lc_number . '.pdf', $pdf);
                    $return['code'] = 200;
                    $return['msg'] = '保存成功, 货代：博佳图';
                    $return['lc_number'] = $order->lc_number;
                    $return['pdf_url'] = $this->$base_pdf_url.'/th-single-plane-bjt?id=' . $order->id;; 
                    $shipping_ok = true;
                }else{
                    $return['code'] = 500;
                    $return['msg'] = $response->message.'<br>'.($response->Error)->errMessage;
                }
            } catch (\SoapFault $e) {
                $return['code'] = 500;
                $return['msg'] = '系统错误：' . $e;
            }
            break;


            default:
                $order->save();
                $return['code'] = 500;
                $return['msg'] = '保存成功, 货代：' . $order->lc;
                $shipping_ok = true;
            break;
        }// end of switch ($order->lc)

 
        //记录日志
        $comment = "订单状态：[{$old_status}] >> [{$order->status}], 贷代：[{$old_lc}] >> [{$order->lc}], 物流单号: [{$old_lc_number}] >> [{$order->lc_number}]。重量：{$weight}";

        $order->setOrderStatus($order_id,$old_status,$order->status,$comment, Yii::$app->user->id);
        $return['shipping_ok'] = $shipping_ok;
        return $return;

    }










    /**
     * comOne 物流东马与西马国家代码
     * @param $post_code
     * @return string
     */
    public function getMyCode($post_code)
    {
        $post_code = trim($post_code);
        if ($post_code >= 87000 && $post_code < 100000) {
            return 'MY1';//东马
        } else {
            return 'MY2';//西马
        }
    }
    /**
     * 商壹各国物流渠道
     * @var array
     */
    public $channelIdArray = [
        'SG' => [
            'NJV' => [
                'M' => [478, 'ECOM-NJV-M'],
                'P' => [477, 'ECOM-NJV-P']
            ]
        ],
        'MY' => [
            'GDEX' => [
                'M' => [437, 'ECOM-COD-GD'],
                'P' => [436, 'ECOM-COD-GP']
            ],
            'DHL' => [
                'M' => [371, 'ECOM-GMS-M'],
                'P' => [370, 'ECOM-GMS-P']
            ]

        ],
        'TH' => [
            'KERRY' => [
                'M' => [407, 'ECOM-TH-M'],
                'P' => [406, 'ECOM-TH-P']
            ],
            'NJV' => [
                'M' => [478, 'ECOM-NJV-M'],
                'P' => [477, 'ECOM-NJV-P']
            ]
        ]
    ];


    /**
     * get channel id
     * @return bool
     */
    public function getChannelId($country,$channel_type,$post_code)
    {
        if ($country == 'SG') {
            return $this->channelIdArray[$country]['NJV'][$channel_type][0];
        } else {
            $model = new WmsChannelServices();
            $channel = $model->getChannelByPostCode($country, $post_code);
            if ($channel) {
                return $this->channelIdArray[$country][$channel][$channel_type][0];
            } else {
                return false;
            }
        }
    }


    public function curl_file_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 抓取结果直接返回（如果为0，则直接输出内容到页面）
        curl_setopt($ch, CURLOPT_HEADER, 0);// 不需要页面的HTTP头
        curl_setopt($ch, CURLOPT_URL, $url);  // 设置要抓取的页面地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output == false) {
            print_r(curl_error());
        }
        return $output;
    }

    /**
     * @var array 各国区号
     */
    public $getPhone = [
        'HK' => '+852',
        '臺灣' => '+886',
        'MY' => '+60',
        'SG' => '+65',
        'TH' => '+66',
        'ID' => '+62',
        'UAE' => '+971',
        'PHL' => '+63',
        'LKA' => '+94',
        'VN' => '+84',
        'KH' => '+855',
    ];

    /**
     * 货代
     * @var array
     */
    public $trackCompany = [
        '商壹' => '商壹',
        'TTI' => 'TTI',
        '汉邮' => '汉邮',
        '森鸿' => '森鸿',
        'imile' => 'imile',
        '云路' => '云路',
        '皇家物流' => '皇家物流',
        '鸿速达' => '鸿速达',
        '1+1' => '1+1',
        '东丰物流' => '东丰物流',
        '博佳图' => '博佳图',
        '易速配'   => '易速配',
        'CDS' => 'CDS',
        'TJM' => 'TJM',
        'K1' => 'K1',
        'AFL' => 'AFL',
        '合联' => '合联',
        '和洋运通' => '和洋运通',
        '顺丰' => '顺丰',
        'pt.zoomy'=>'pt.zoomy',
    ];

    public $getCountry = [
        'HK' => '香港',
        '臺灣' => '臺灣',
        'MY' => '马来西亚 Malaysia',        //MY0  西马     MY1  东马
        'SG' => '新加坡 Singapore',
        'TH' => '泰国 Thailand',
        'ID' => '印尼',
        'UAE' => '阿联酋',
        'PHL' => '菲律宾',
        'LKA' => '斯里兰卡',
        'VN' => '越南',
        'KH' => '柬埔寨',
    ];

    /**
     * 汇率
     * @var array
     */
    public $converter = [
        'MY' => 0.23655,
        'SG' => 0.73439,
        'TH' => 0.03011,
        'ID' => 0.00007355645,
        'UAE' => 0.27231,
        'TW' => 0.03378,
        'HK' => 0.12781,
        'PHL' => 0.01921,
        'LKA' => 0.006427,
        'VN' => 0.00004392,
        'KH' => 1,
    ];

    public $currencyType = [
        'MY' => 'MYR',
        'SG' => 'SGD',
        'TH' => 'THB',
        'ID' => 'IDR',
        'UAE' => 'AED',
        'TW' => 'TWD',
        'PHL' => 'PHP',
        'LKA' => 'LKR',
        'HK' => 'HKD',
        'VN' => '₫',
        'KH' => '$',
    ];

    public $getLw = [
        'NINJAVAN' =>  'NINJAVAN',
        'KERRYEXPRESS' =>  'KERRYEXPRESS',
        'J&T' =>  'J&T',
        'JNE' =>  'JNE',
        'DHL' =>  'DHL',
        'GDEX' =>  'GDEX',
        'POST_TW' =>  'POST_TW',
        'SAP' =>  'SAP',
        'FFC' =>  'FFC',
        'ROADBULL' =>  'ROADBULL',
        'ALPHA_FAST' =>  'ALPHA_FAST',
        '高盛' =>  '高盛',
    ];

    /**
     * 获取落地配
     * @param $track_number
     * @param $country
     * @param $lc
     * @return string
     */
    public function getLw($track_number,$country,$lc){
        if(strtoupper(substr($track_number, 0, 5)) == 'PFCSG'){
            $action = 'NINJAVAN';
        }elseif ($country == 'TH' && $lc == '博佳图'){
            $action = 'KERRYEXPRESS';
        }elseif($country == 'ID' && $lc =='云路'){
            $action = 'J&T';
        }elseif($country == 'ID' && $lc =='CDS'){
            $action = 'JNE';
        }elseif($country == 'TH' && $lc =='TTI'){
            $action = 'KERRYEXPRESS';
        }elseif($country == 'MY' && $lc =='1+1' && strtoupper(substr($track_number, 0, 5)) == 'YJYMY'){
            $action = 'NINJAVAN';
        }elseif ($country == 'ID' && $lc == '汉邮'){
            $action = 'J&T';
        }elseif ($country == 'MY' && $lc == 'CDS' ){
            $action = 'DHL';
        }elseif (strtoupper(substr($track_number, 0, 5)) == 'PFCMY') {
            $action = 'NINJAVAN';
        }elseif (substr($track_number, 0, 3) == '744') {
            $action = 'NINJAVAN';
        }elseif ($country == 'SG' && '745' == substr($track_number, 0, 3)) {
            $action = 'NINJAVAN';
        }elseif (strtoupper(substr($track_number, 0, 3)) == 'MYA') {
            $action = 'DHL';
        }elseif ($country == 'MY' && '745' == substr($track_number, 0, 3)) {
            $action = 'DHL';
        }elseif ($country == 'TH' && strtoupper(substr($track_number, 0, 4)) == 'SOAR') {
            $action = 'KERRYEXPRESS';
        }elseif ('TH' == $country && strtoupper(substr($track_number, 0, 3)) == 'TTI') {
            $action = 'KERRYEXPRESS';
        }elseif (($country == 'TH' && (strtoupper(substr($track_number, 0, 3)) == 'PFC' || is_numeric($track_number))) || ($lc == '1+1' && $country == 'MY') || ($lc == '1+1' && $country == 'SG') || ($country == 'MY' && substr($track_number, 0, 2) == '87')) {
            if ($lc == '1+1' && $country == 'MY') {
                if (substr($track_number, 0, 3) == 'YJY') {
                    $action =  null;
                } else {
                    $action =  'GDEX';
                }
            } elseif ($lc == '1+1' && $country == 'SG') {
                $action = 'ROADBULL';
            } elseif ($lc == '商壹' && $country == 'MY' && substr($track_number, 0, 2) == '87') {
                $action = 'GDEX';
            } elseif($lc == '商壹' && $country == 'MY' && substr($track_number, 0, 3) == '745'){
                $action = 'DHL';
            } else {
                $action = 'ALPHA_FAST';
            }
        }elseif($lc == '东丰物流' && $country == 'MY'){
            $action = 'DHL';
        }elseif (substr($track_number, 0, 3) == 'YJY'){
            $action = 'GDEX';
        }elseif ($country == '臺灣' && $lc == '易速配'){
            $action = 'POST_TW';
        }elseif ($country == '臺灣' && $lc == '1+1'){
            $action = '黑猫';
        }elseif($lc == 'AFL' && $country == 'ID'){  //香港高盛   印尼SAP
            $action = 'SAP';
        }elseif($lc == 'AFL' && $country == 'HK'){  //香港高盛   印尼SAP
            $action = '高盛';
        }elseif($lc == '合联'){
            $action = 'FFC';
        }elseif($lc == '和洋运通' && $country == 'PHL'){
            $action = 'NINJAVAN';
        }elseif($lc == 'K1'){
            $action = 'DHL';
        }else{
            $action = null;
        }
        return $action;
    }


    public $getLcV = [
        1 => '汉邮',
        2 => '汉邮',
    ];

}
