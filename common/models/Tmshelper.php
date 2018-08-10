<?php

namespace common\models;

use Yii;
use common\models\TrackLog;
/*use app\models\Orders;
use app\models\OrderLogs;
use app\models\Stocks;*/

/**
 * 这里是跟踪物流的方法总类， 用来跟踪订单物流，保存到 tracklog 表
 *
 * @property string $id
 * @property string $name
 * @property string $photo
 * @property string $message
 * @property string $picked
 * @property string $create_time
 */
class Tmshelper
{
    //核心函数，用来跟踪订单物流，保存到 tracklog 表
    public function logTrackingInfo($order)
    {
        //第一步，选择物流，获得跟踪信息   
        //第二步，保存物流信息  
        switch ($order->county) {
            case 'TH':
                $ret = this->com1express($order->lc_number,$order->id,$order->county);
                break;
            
            default:
                # code...
                break;
        }

        //第三步，返回物流最后状态 getStatus
        $this->getStatus($order->id);
        //第四步，设置订单状态 setOrderStatus
        $this->setOrderStatus($order->id);
    }
 


    //第三步，返回物流最后状态 getStatus            
    public function getStatus($order_id){
        // actionUpdateOrderTrackStatus 函数
    }

    //第四步，设置订单状态 setOrderStatus
    public function setOrderStatus($order_id){   

        $order = Orders::findOne($order_id);
            if ($order->status == '已签收' || $order->status == '拒签') {
                $log = new OrderLogs();
                $log->attributes = [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'user_id' => 1,
                    'create_date' => date('Y-m-d'),
                    'comment' => '抓取物流改状态为' . $order->status,
                ];
                $log->save();
            }
    }


    ///////////// 以下为各大物流的跟踪////////////////////////////


    


    //Yunlu   track and log
    public function Yunlu($track_number,$order_id,$country){

        if ($country == 'ID') {
            $yl = new Yunlu();
            $logs = $yl->track($track_number); 
            if (isset($logs->responseitems)) {

                $track_date = null;
//                        print_r($logs->responseitems[0]->tracesList[0]->details);
//                        exit;
                foreach ($logs->responseitems[0]->tracesList[0]->details as $track_log) {
                    $status = trim($track_log->scantype);
                    $track_date = date('Y-m-d H:i:s', strtotime($track_log->scantime));

                    $this->save_track_log($order_id,$track_date,$status,$remark='',$country,$track_number );


//                            if ($track_log->scantype == '末端疑难件' || '末端留仓件' == $track_log->scantype || '拒签' == $track_log->scantype) {
//                                $order->status = '拒签';
//                                $order->delivery_date = $track_date;
//                                $order->track_status = 'Exception';
//                                if ($stock_order) {
//                                    $stock_order->status = 4;
//                                    $stock_order->save();
//                                }
//
//                                $order->save();
//                            } elseif ($track_log->scantype == '末端签收') {
//                                $order->status = '已签收';
//                                $order->delivery_date = $track_date;
//                                $order->track_status = 'Delivered';
//                                if ($stock_order) {
//                                    $stock_order->status = 3;
//                                    $stock_order->save();
//                                }
//                                $order->save();
//                            }
                            //货代收件
                    $order = Orders::findOne($order_id);
                    if ($track_log->scantype == '收件') {
                        $order->shipment_picked_up_date = $track_date;
                        $order->save();
                    }
                            //派送公司收件
                    if ($track_log->scantype == '末端到件') {
                        $order->pickup_date = $track_date;
                        $order->save();
                    }
                            //echo $track_log->scantype;
                }
            }
        }// if country ID
    }


 

    //以下是 新的tracking
    //Trackingmore 接口   track and log
    public function Trackingmore($track_number,$order_id,$country,$lc){
        $track = new Trackingmore;
        switch ($lc)
        {
            case '1+1':
            if($country == 'SG')
            {
                $output = $track->getRealtimeTrackingResults('roadbull', 'RB_JHJ' . $track_number);
            }
            break;
            case '商壹':
            if($country == 'MY' && '745' == substr($track_number, 0, 3))
            {
                $output = $track->getRealtimeTrackingResults('dhlecommerce-asia', 'MYAMB' . $track_number);
            }
            break;
            case 'CDS':
            if($country == 'MY')
            {
                $output = $track->getRealtimeTrackingResults('dhlecommerce-asia', $track_number);
            }
            break;
            case '东丰物流':
            if($order->county == 'MY')
            {
                $output = $track->getRealtimeTrackingResults('dhlecommerce-asia', $track_number);
            }
            break;
            case 'TJM':
            if($order->county == 'MY')
            {
                $output = $track->getRealtimeTrackingResults('dhlecommerce-asia', $track_number);
            }
            break;
            case 'K1':
            if($order->county == 'MY')
            {
                $output = $track->getRealtimeTrackingResults('dhlecommerce-asia', $track_number);
            }
            break;
            default:
            $output = $track->getRealtimeTrackingResults('alpha-fast', $track_number);
        }


        $status = 'Pending';
        if (isset($output['data']['items']) && $output['data']['items'][0]['origin_info']['trackinfo']) {

            switch (strtolower($output['data']['items'][0]['status'])) {
                case 'delivered':
                $status = 'Delivered';
                break;
                case 'undelivered':
                $dd_fail = 1;
                $status = 'Pending';
                break;
                case 'exception':
                $status = 'Exception';
                $dd_fail = 0;
                break;
                case 'notfound':
                $status = 'No-info';
            }

            if ($output['data']['items'][0]['lastUpdateTime']) {
                $delivery_date = $output['data']['items'][0]['lastUpdateTime'];
            }
            if ($output['data']['items'][0]['origin_info']['ItemReceived']) {
                $pickup_date = date('Y-m-d H:i:s', strtotime($output['data']['items'][0]['origin_info']['ItemReceived']));
            }

            foreach ($output['data']['items'][0]['origin_info']['trackinfo'] as $trackinfo) {
                $track_date = date("Y-m-d H:i:s", strtotime($trackinfo['Date']));
                $track_status = trim($trackinfo['StatusDescription']);

                $this->save_track_log($order_id,$track_date,$track_status,$remark='',$country,$track_number );
            }
        }
        return $status;
    }

    //ninjavan  track and log
    public function ninjavan($track_number,$order_id,$country){
        switch ($country)
        {
            case 'MY':
            case 'Malaysia':
            $url = 'https://api.ninjavan.co/my/shipperpanel/app/tracking?&id=';
            break;
            case 'SG':
            case 'Singapore':
            $url = 'https://api.ninjavan.co/sg/shipperpanel/app/tracking?&id=';
            break;
            default:
            $url = 'https://api.ninjavan.co/sg/shipperpanel/app/tracking?&id=';
            break; 
        }
        $curl_url = $url.$track_number;
        $output = $this->curl_info($curl_url);

        $data = json_decode($output, true);
        if (!empty($data)) {
            $status = $data['orders'][0]['status'];
                    //$postcode = $data['orders'][0]['to_postcode'];
            //     $to_address = $data['orders'][0]['to_address1'];
            $granular_status = $data['orders'][0]['granular_status'];
            $transactions = $data['orders'][0]['transactions'];
            $last_trans = $transactions[count($transactions) - 1];
            $pickup_date = date('Y-m-d H:i:s', strtotime($transactions[0]['service_end_time']));

            foreach ($transactions as $trans) {
                $track_date = date('Y-m-d H:i:s', strtotime($trans['service_end_time']));
                $track_status = trim($trans['type'].' '.$trans['status']);

                $this->save_track_log($order_id,$track_date,$track_status,$remark='',$country,$track_number );


                if ($trans['type'] == 'DD' && $trans['status'] == 'Fail') {
                    $dd_fail++;
                }
            }

            /**
             * 添加最后签收结果
             */
            $logModel = new TrackLog();    
            $md5 = md5(json_encode(['order_id' => $order->id,
                        'track_date' => $track_date,
                        'track_status' => $granular_status]));
            if (!$logModel->find()->where(['md5' => $md5])->one()) {
                $logModel->attributes = [
                            'order_id' => $order->id,
                            'track_date' => $track_date,
                            'track_status' => $granular_status,
                            'md5' => $md5,
                            'destination' => $order->county,
                ];
                $logModel->setIsNewRecord(true);
                unset($logModel->id);
                if($logModel->save())
                {
                    echo '.';
                }
            }
            //确定最后状态
            switch ($status) {
                        case 'Completed':
                        $status = 'Delivered';
                        $delivery_date = date('Y-m-d H:i:s', strtotime($last_trans['service_end_time']));
                        break;
                        case 'Delivery fail':
                        $status = 'Pending';
                        break;
                        case 'Cancelled':
                        $status = 'Exception';
                        break;
                        default:
                        $status = 'Pending';
            }

            if ($last_trans['status'] == 'On Hold') {
                        $status = 'On Hold';
            }
            if ($granular_status == 'Returned to Sender') {
                        $status = 'Exception';
                        $dd_fail = 0;
            }
        } else {// no data
            $status = 'No-info';
        }
        return $status;
    }



    //dhl  track and log  //TODO 不完整
    public function dhl($track_number,$order_id,$country){
        switch ($country)
        {
            case 'SG':
            $url = 'https://webtrack.dhlglobalmail.com/?locale=en-US&trackingnumber=';
            break;
            default:
            $url = 'https://webtrack.dhlglobalmail.com/?locale=en-US&trackingnumber=';
            break; 
        }
        $curl_url = $url.$track_number;
        $output = $this->curl_info($curl_url);

        preg_match('/<h2>(.+)<\/h2>/i', $output, $match);
        preg_match_all('/<li class="timeline-date">(.+?)<\/li>/is', $output, $timeline_date);
        if ($timeline_date[1]) {
            $pickup_date = date('Y-m-d H:i:s', strtotime($timeline_date[1][count($timeline_date[1]) - 1]));
        }

        $status = isset($match[1]) ? $match[1] : 'No-info';
        if (($status == 'Delivered' || $status == 'Exception') && $timeline_date[1]) {
            $delivery_date = date('Y-m-d H:i:s', strtotime($timeline_date[1][0]));
        }
        if ($status == 'En Route') {
            $status = 'Pending';
        }
        $track_date =  $delivery_date ? $delivery_date : null;
        $this->save_track_log($order_id,$track_date,$status,$remark='',$country,$track_number );
 
        return $status;
    }


    //kerryexpress   track and log
    public function kerryexpress($track_number,$order_id,$country){
        switch ($country)
        {
            case 'TH':
            $url = 'https://th.kerryexpress.com/en/track/?track='; 
            break;
            default:
            $url = 'https://th.kerryexpress.com/en/track/?track='; 
            break; 
        }
        $curl_url = $url.$track_number;
        $output = $this->curl_info($curl_url);
        preg_match_all('/<div class="date">\n*\s*<div>(.+?)<\/div>\n*\s*<div>(.+?)<\/div>\n*\s*<\/div>/i', $output, $dates);
        $status = 'Pending';
        if ($dates[1]) {
            $date_str = trim(str_replace('Date', '', $dates[1][count($dates[1]) - 1]));
            $time_str = trim(str_replace('Time', '', $dates[2][count($dates[2]) - 1]));
            $pickup_date = date('Y-m-d H:i:s', strtotime($date_str . ' ' . $time_str));
        }
        preg_match_all('/<div class="d1">(.+?)<\/div>/is', $output, $status_info);
        if ($status_info[1]) {
            foreach ($status_info[1] as $key => $track) {
                $track = trim($track);
                $date_str = trim(str_replace('Date', '', $dates[1][$key]));
                $time_str = trim(str_replace('Time', '', $dates[2][$key]));
                $track_date = date('Y-m-d H:i:s', strtotime($date_str . ' ' . $time_str));

                if (strpos($track, 'Delivery Successful') !== false) {
                    $track = 'Delivery Successful';
                    $status = 'Delivered';
                    $delivery_date = date('Y-m-d H:i:s', strtotime($date_str . ' ' . $time_str));
                } elseif (strpos($track, 'Undelivered shipment return to origin') !== false) {
                    $status = 'Exception';
                    $dd_fail = 0;
                    $delivery_date = date('Y-m-d H:i:s', strtotime($date_str . ' ' . $time_str));
                }
                $this->save_track_log($order_id,$track_date,$track,$remark='',$country,$track_number );
            }
        } else {
            $status = 'No-info';
        }
        return $status;
    }


    //GInfo  track and log
    public function GInfo($track_number,$order_id,$country){
        switch ($country)
        {
            case '臺灣':
                $url = 'http://120.79.18.139/cgi-bin/GInfo.dll?EmsApiTrack&ntype=10100&cp=65001&cno='; 
                break;
            default:
                $url = 'http://120.79.18.139/cgi-bin/GInfo.dll?EmsApiTrack&ntype=10100&cp=65001&cno='; 
                break; 
        }
        $curl_url = $url.$track_number;
        $output = $this->curl_info($curl_url);
        $status = 'Pending';   
        $output = json_decode($output,true);  
        if (isset($output['ReturnValue']) && $output['ReturnValue'] == 100) {
            if($output['Response_Info']['deliveryDate']) {
                $status = 'Delivered'; 
            }else{
                $status = 'Pending';
            }
            if ($output['Response_Info']['deliveryDate']) { 
                $delivery_date = date('Y-m-d H:i:s', strtotime(str_replace('.', '-',$output['Response_Info']['deliveryDate']) ));
            }
            if ($output['Response_Info']['pickupDate']) {
                $pickup_date = date('Y-m-d H:i:s', strtotime(str_replace('.', '-',$output['Response_Info']['pickupDate'])));
            }
            $last_track_status = ''; //默认没有物流信息
            foreach ($output['trackingEventList']  as $trackinfo) { 
                $track_date = date("Y-m-d H:i:s", strtotime(str_replace('.', '-',$trackinfo['date'])));
                $track_status = trim($trackinfo['details']);
                $this->save_track_log($order_id,$track_date,$track_status,$remark='',$country,$track_number );
                $last_track_status = $track_status;
            } 

            //last status TODO
            $last_track_status =  mb_convert_encoding($last_track_status, "utf-8") ;   
            switch ($last_track_status) {
                case '到達支局招領中':
                $status = 'Pending';
                break;
                case '招領郵件轉運中':
                $status = 'Pending';
                break;    
                case '投遞不成功':
                $status = 'Exception';
                $dd_fail = 0;
                break;
                case '安排投遞中':
                $status = 'Pending';
                $dd_fail = 0;
                break;

                case '交寄郵件':
                $status = 'Pending';
                break;
                case '運輸途中':
                $dd_fail = 0;
                $status = 'Pending';
                break;
                case '到達投遞局':
                $status = 'Pending';
                $dd_fail = 0;
                break;
                case '郵件投遞中':
                $status = 'Pending';
                $dd_fail = 0;
                break;
                case '投遞成功':
                $status = 'Delivered';
                $dd_fail = 0;
                break;
                case '入帳成功': 
                $status = 'Delivered';
                $dd_fail = 0;
                break;

                default:  
                $status = 'Exception';
                $dd_fail = 0;
                break;
            }
        }
        return $status;
    }



    //Tcat  track and log
    public function tcat($track_number,$order_id){
        //TODO https://www.t-cat.com.tw/Inquire/statuslist.aspx  物流状态和系统的对应
        //数据抓取
        $curl_url = 'https://www.t-cat.com.tw/Inquire/TraceDetail.aspx?ReturnUrl=Trace.aspx&BillID='.$track_number;
        $output = $this->curl_info($curl_url);
        preg_match_all("/<tr valign='center' align='middle' bgcolor='(.+?)'>(.+?)<\/tr>/i", $output, $dates);
        $info_list = $dates[2];
        $info_arr = [];
        foreach ($info_list as $key => $value) {
            preg_match_all("/<td class='style1'(.+?)>(.+?)<\/td>/i", $value, $date); 
            $each_item = [];
            $do_data = $date[2];
            foreach ($do_data as $k => $v) {
                $each_item[$k] = trim(strip_tags($v)); 
            }
            $info_arr[$key] = $each_item;
        }
        //数据处理
        if (isset($info_arr[0])) {
            $status = 'Pending';
            $first_item = end($info_arr);
            if ($first_item) { 
                $pickup_date = date('Y-m-d H:i:s', strtotime($first_item[1]));
            }
            foreach ($info_arr as $key => $val) {
                
                $track_date = date('Y-m-d H:i:s', strtotime($val[1]));
                $track_status = $val[0];
                $destination = '';
                $this->save_track_log($order_id,$track_date,$track_status,$remark='',$destination,$track_number );
                $status = $track_status;
                if (strpos($status, '順利送達') !== false) {
                    $status = 'Delivered';
                    $delivery_date = date('Y-m-d H:i:s', strtotime($val[1]));
                }
            }
        } else {
            $status = 'No-info';
        }
        return $status;
    }


    //AFL track and log
    //参数$order_id 作为 记录标识作用
    public function AFL($track_number,$order_id){
        $uid = 'afl845753';
        $sign = 'cedd8618ed89e6fe8445d50534a0ba75';
        //   $language = 'zh-cn'; //  语言版本 zh-cn 返回中文轨迹， en 返回英文轨迹，默认英文轨迹
        $language = 'en'; //  语言版本 zh-cn 返回中文轨迹， en 返回英文轨迹，默认英文轨迹
        $url = 'http://47.75.39.59/api/query_track?uid='.$uid.'&sign='.$sign.'&no='.$track_number.'&l='.$language;
        //   $url = 'http://47.75.39.59/api/query_track?uid='.$uid.'&sign='.$sign.'&no=AFLAA0000000002YQ'.''.'&l='.$language;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 抓取结果直接返回（如果为0，则直接输出内容到页面）
        curl_setopt($ch, CURLOPT_HEADER, 0);// 不需要页面的HTTP头
        curl_setopt($ch, CURLOPT_URL, $url);  // 设置要抓取的页面地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output,true);
        $status = 'Pending';
        if(isset($output['status'])) {
            if ($output['status'] == 200) {
                $code = [];
                foreach ($output['tracks'] as $v) {
                    if ($v['createTime'] < 1) {
                        $v['createTime'] = 1;
                    }
                    $track_date = date('Y-m-d H:i:s', $v['createTime']);
                    $track_status = $v['content'];
                    $destination = '轨迹发生的地点:' . $v['place'];
                    $remark = $v['code'];
                    $this->save_track_log($order_id,$track_date,$track_status,$remark,$destination,$track_number);
                }
                $status = $track_status;

            } else {
                $status = 'No-info';
            }
        }else{
            $status = 'No-info';
        }
        return $status;
    }


    //合联  track and log
    public function helian($track_number){
        if(strlen(str_replace('CNB','',$track_number)) == strlen($track_number)){
            $url = 'http://track.winlinklogistics.com/v1/order2?17no='.$track_number;
        }else{
            $url = 'http://track.winlinklogistics.com/v1/order2?awbno='.$track_number;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 抓取结果直接返回（如果为0，则直接输出内容到页面）
        curl_setopt($ch, CURLOPT_HEADER, 0);// 不需要页面的HTTP头
        curl_setopt($ch, CURLOPT_URL, $url);  // 设置要抓取的页面地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output,true);
        
        if($output['error'] == 0 && is_array($output['data'])){
            foreach ($output['data'] as $v){
                $track_date = $v['TransDate'].' '.$v['TransTime'];
                $track_status = $v['Remarks'];
                $destination = $v['Destination'];
                $remark = $v['status'];
                $status_cn = $v['Location'];
                if($pickup_date == null){
                    $pickup_date = $track_date;
                }
                $pickup_date = strtotime($pickup_date) > strtotime($track_date) ?  $track_date : $pickup_date;

                $this->save_track_log($order_id,$track_date,$track_status,$remark,$destination,$track_number,$status_cn);

                //Last Status TODO
                if($track_log->getStatus($remark) == '派送失败'){
                    $dd_fail += 1;
                }
                if(!isset($status)){
                    if(in_array($track_log->getStatus($remark),['已签收','拒签'])){
                        $status = $track_log->getStatus($remark) == '已签收' ? 'Delivered' : 'Exception';
                    }
                }
            }
            //Last Status TODO
            if(!isset($status)){
                $status = 'Pending';
            }
        }else {
            $status = 'No-info';
        }
        return $status;
    }





    //商壹   track and log
    public function com1express($track_number,$order_id,$country){
        $key = '34947aeefa6d364dba91ffb48c032202';
        $customercode = '610212';
        $url = 'http://www.com1express.net/api/getPodstatus.html?key=' . $key . '&waybillnum=' . $track_number . '&customercode=' . $customercode;
        $response = $this->curlGet($url);
        $response_json = json_decode($response, true);

        if ($response_json) {
            foreach ($response_json as $data) {
                $status = trim($data['podstatus']);
                $track_date = $data['date'] . ' ' . $data['time'];

                $this->save_track_log($order_id,$track_date,$status,$data['remark'],$data['destinationCountry'],$track_number,'',$data['position'] );
 
                //订单和海外仓订单 改变状态
                $order = Orders::findOne($order_id);
                $stockModel = new Stocks();
                $stock_order = $stockModel->find()->where(['new_order_id' => $order->id])->one();

                if ($data['podstatus'] == 'Processed at facility' || $data['podstatus'] == 'Picked up by courier') {
                    $order->pickup_date = $track_date;
                }
                if ($data['podstatus'] == 'Shipment Picked Up') {
                    $order->shipment_picked_up_date = $track_date;
                }

                switch ($data['podstatus']) {
                    case 'Completed':
                    case 'Delivered':
                    case 'Shipment Delivered':
                    case 'Successfully delivered':
                    $order->status = '已签收';
                    $order->delivery_date = $track_date;
                    $order->track_status = 'Delivered';
                    if ($stock_order) {
                        $stock_order->status = 3;
                        $stock_order->save();
                    }
                    break;
                    case 'Delivery Refused':
                    case 'Return to Sender':
                    case 'Returned to Warehouse':
                    case 'Return shipment was successfully delivered':
                    case 'Return shipment being processed':
                    $order->status = '拒签';
                    $order->delivery_date = $track_date;
                    $order->track_status = 'Exception';
                    if ($stock_order) {
                        $stock_order->status = 4;
                        $stock_order->save();
                    }
                    break;
                }
            }

            $order->save();
        } 
        return $status;
    }


    //1+1   track and log
    public function oneone($track_number,$order_id,$country){
        $track = new Trackingmore; 
        $output = [];

        switch ($country) {
            case 'TH':
            if ('TTI' == substr($track_number, 0, 3)) {
                $output = $track->getRealtimeTrackingResults('kerry-logistics', $track_number);
            }
            break;
            case 'MY':
            if ('YJY' == substr($track_number, 0, 3)) {
                return false;   //物流单号错误
            } else {
                $output = $track->getRealtimeTrackingResults('gdex', $track_number);
            }
            break;
            case 'SG':
                $output = $track->getRealtimeTrackingResults('ninjavan', $track_number);
            break;
        }
        if ($output) {
            if (200 == $output['meta']['code'] && $output['data']['items'][0]['origin_info']['trackinfo']) {
                foreach ($output['data']['items'][0]['origin_info']['trackinfo'] as $trackinfo) {

                    $this->save_track_log($order_id,$trackinfo['Date'],$trackinfo['StatusDescription'],$remark='',$trackinfo['Details'],$track_number );    
                }


                //订单和海外仓订单 改变状态
                $order = Orders::findOne($order_id);
                $stockModel = new Stocks();
                $stock_order = $stockModel->find()->where(['new_order_id' => $order->id])->one();

                $order->pickup_date = $output['data']['items'][0]['origin_info']['ItemReceived'];
                $last_status = $output['data']['items'][0]['lastEvent'];
                switch ($output['data']['items'][0]['status']) {
                    case 'delivered':
                    $order->status = '已签收';
                    $order->delivery_date = $output['data']['items'][0]['origin_info']['DestinationArrived'];
                    $order->track_status = 'Delivered';
                    if ($stock_order) {
                        $stock_order->status = 3;
                    }
                    break;
                    case 'undelivered':
                    case 'exception':
                    $order->status = '拒签';
                    $order->delivery_date = $output['data']['items'][0]['origin_info']['DestinationArrived'];
                    $order->track_status = 'Exception';
                    if ($stock_order) {
                        $stock_order->status = 4;
                    }
                    break;
                    default:
                    if (strpos($last_status, 'Refer to Sender') !== false) {
                        $order->status = '拒签';
                        $order->delivery_date = $output['data']['items'][0]['origin_info']['DestinationArrived'];
                        $order->track_status = 'Exception';
                        if ($stock_order) {
                            $stock_order->status = 4;

                        }
                    }
                    break;
                }
                if ($stock_order) {
                    $stock_order->save();
                }
                $order->save();

                $status = $output['data']['items'][0]['status'];
            } else {
                $status = $output['meta']['message'];
            }

        }
        return $status;
    }
 






    //保存track跟踪记录
    public function save_track_log($order_id,$track_date,$track_status,$remark='',$destination,$track_number,$status_cn='',$mailing=''){
        $ret = false;
        $md5 = md5(json_encode([
            'order_id' => $order_id,
            'track_date' => $track_date,
            'track_status' => $track_status])); 
        if (!(Yii::$app->db->createCommand("select id from track_log where md5 = '{$md5}'")->queryOne())) {
            $ret = Yii::$app->db->createCommand()->insert("track_log", [
                'order_id' => $order_id,
                'track_date' => $track_date,
                'track_status' => $track_status,
                'md5' => $md5,
                'remark' => $remark,
                'destination' => $destination,
                'status_cn' => $status_cn,
                'mailing' => $mailing,
                'track_number' => $track_number
            ])->execute();
        }
        return $ret;
    }
    





    //curl_info 接口
    public function curl_info($lc_number){
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 抓取结果直接返回（如果为0，则直接输出内容到页面）
            curl_setopt($ch, CURLOPT_HEADER, 0);// 不需要页面的HTTP头
            curl_setopt($ch, CURLOPT_URL, $url);  // 设置要抓取的页面地址
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $output = curl_exec($ch);
            return $output;
    }
    /**
     * GET CURL
     * @param $url
     * @return bool|mixed
     */
    public function curlGet($url)
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
        if ($output === FALSE) {
//            echo "cURL Error: " . curl_error($ch);
            return false;
        } else {
            return $output;
        }
    }


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
}
