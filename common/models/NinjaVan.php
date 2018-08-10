<?php
/**
 * NinjaVan订单系统
 * Created by Liangdabiao.
 * 文档地址： https://ninjaorderapibeta.docs.apiary.io/#reference/0//{countrycode}/2.0/oauth/access_token/post
 * 这里还要缓存token ，过期更新
 */

namespace app\models;

use Yii;
use common\models\WmsSoBill; 
use common\models\WmsSoBillDetail; 
use common\models\WmsOrderPackageWz;
use common\models\WmsProduct;

class NinjaVan
{
    private $client_id = "1884cc14d89645c3bb6bae7a80f9f14b";
    private $client_secret = "9f8c69154032401d85d5298f29ce6a51";
    private $country_code = "SG"; // your country code
    private $prefix = 'AFLAA-';
    private $ninjavan_site = "https://api-sandbox.ninjavan.co/";  //正式环境为 "https://api.ninjavan.co/"
    
    //设置国家 Possible values:  SG , MY , TH , ID , VN , PH , MM .  TODO
    public function set_country_code($county){
        $country_code = "";
        $county = strtoupper($county);
        switch ($county) {
            case 'SG':
                $country_code = "SG";
                break;
            case 'MY':
                $country_code = "MY";
                break;
            case 'TH':
                $country_code = "TH";
                break;
            case 'ID':
                $country_code = "ID";
                break;
            case 'VN':
                $country_code = "VN";
                break;
            case 'PHL':
                $country_code = "PH";
                break;
            case 'MM':
                $country_code = "MM";
                break;
            
            default:
                # code...
                break;
        }


        if($country_code){
            $this->country_code = $country_code;
        }
    }

    public function accessToken(){
        // 尝试从缓存中取回 $$key 
        $key = 'NinjaVan_accessToken';
        $cache = Yii::$app->cache;
        $accessToken_data = $cache->get($key);

        if ($accessToken_data === false) {

            $token_url = $this->ninjavan_site.$this->country_code."/2.0/oauth/access_token";

            $data = array("client_id" => $this->client_id, "client_secret" => $this->client_secret, "grant_type" => "client_credentials");

            $data_string = json_encode($data); 

            $curl = curl_init($token_url);

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

            $response = curl_exec($curl);
            $error = curl_error($curl);

            curl_close($curl);

            if($error){
              $requestError = $error;
              echo "Curl Error Occured:". $error;
              return false;

          }else{
              $response = json_decode($response,1); 
              $cache->set($key, $response);
              $bearerToken = $response['access_token']; 
              return $bearerToken;
          }
      }else{
        $bearerToken = $accessToken_data['access_token']; 
            if( time() < $accessToken_data['expires']-1000 ){ //还没有过期 
                return $bearerToken;
            }else{
                $cache->set($key, false);
                return $this->accessToken();
            }
            
        } 
    }


    /**
     * 下单
     * @param $order_id
     * @return mixed
     */
    public function orderCreate($order_id)
    {
        //TODO 发货地址要修改 
        $endpoint_url_production = $this->ninjavan_site.$this->country_code."/4.1/orders"; 



        $timezone = 'Asia/Singapore';
        
        //日期处理 ISO8601
        $datetime = new \DateTime();
        $delivery_start_date = $datetime->format(\DateTime::ATOM); // Updated ISO8601
       

        $order = WmsSoBill::findOne($order_id);
        if ($order) {
            $order_wv = WmsOrderPackageWz::findOne(['order_id' => $order_id]);
            $product = WmsProduct::findOne($order->website);
            $order_items = WmsSoBillDetail::findAll(['order_id' => $order_id]);
            $items = [];
            $en_name = preg_replace('/【.+】/i', '', $product->name);
            
            $county = strtoupper($order->county);
            switch ($county) { ////设置国家时区 Possible values:  SG , MY , TH , ID , VN , PH , MM . //TODO
                case 'SG':
                    $timezone = 'Asia/Singapore';
                    break;
                case 'MY':
                    $timezone = 'Asia/Kuala_Lumpur';
                    break;
                case 'ID':
                    $timezone = 'Asia/Jakarta';
                    break;
                case 'ID':
                    $timezone = 'Asia/Jayapura';
                    break;
                case 'ID':
                    $timezone = 'Asia/Makassar';
                    break;
                case 'TH':
                    $timezone = 'Asia/Bangkok';
                    break;
                case 'PHL':
                    $timezone = 'Asia/Manila';
                    break;
                case 'VN':
                    $timezone = 'Asia/Ho_Chi_Minh';
                    break;
                case 'MM':
                    $timezone = 'Asia/Yangon';
                    break;
                default:
                    # code...
                    break;
            }

            $mobile = preg_replace('/[^\d]/i', '', $order->mobile);
            $requested_tracking_number = time(); 

            $logistics_interface = [
               "service_type"=>"Parcel",
               "service_level"=>"Standard",  
               "requested_tracking_number"=> $requested_tracking_number,
               "reference"=>[
                  "merchant_order_number"=>$this->prefix.$requested_tracking_number
              ],
              "from"=>[
                  "name"=> "John Doe",
                  "phone_number"=> "+60122222222",
                  "email"=> "john.doe@gmail.com",
                  "address"=> [
                    "address1"=> "17 Lorong Jambu 3",
                    "address2"=> "",
                    "area"=> "Taman Sri Delima",
                    "city"=> "Simpang Ampat",
                    "state"=> "Pulau Pinang",
                    "country"=> "SG",
                    "postcode"=> "470717"
                ]
            ],
            "to"=>[
              "name"=> str_replace(['"', "'"], '', $order->name),
              "phone_number"=> $mobile,
              "email"=> $order->email,
              "address"=>[
                "address1"=> $order->address,
                "address2"=> "",
                "kelurahan"=>"Kelurahan Gambir",//只有ID有
                "kecamatan"=>"Kecamatan Gambir",//只有ID有
                "city"=> $order->city,
                "province"=> $order->district,
                "country"=> $order->county,
                "postcode"=>  $order->post_code
            ]
        ],
        "parcel_job"=>[
          "is_pickup_required"=> false,
          "cash_on_delivery"=> 24,

          "dimensions"=>[
            "weight"=> $order_wv->weight
            ],
            "pickup_instruction"=> "Pickup with care!",
            "delivery_instruction"=> "If recipient is not around, leave parcel in power riser.",
            "delivery_start_date"=> $delivery_start_date,
            "delivery_timeslot"=>[
                "start_time"=> "09:00",
                "end_time"=> "22:00",
                "timezone"=> $timezone
                ]
          ]
        ];


//注意：
//service_level  Must be either SAMEDAY, NEXTDAY, EXPRESS or STANDARD
  //pickup_address_id 
// "kelurahan": "kecamatan": 只有ID有
//"delivery_instruction": 给司机的备注
//"timezone":  根据各国家来   
$bearerToken = $this->accessToken();
 $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $endpoint_url_production);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
  curl_setopt($ch, CURLOPT_HEADER, FALSE);

  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($logistics_interface) );

  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Authorization: Bearer ".$bearerToken
  ));

  $response = curl_exec($ch);
  $err = curl_error($ch);
  curl_close($ch); 
  if($err){

    
    $resl['error'] = "CURL ERROR:" . $err;
    

  }else{
        $response = json_decode( $response,1);
     if($response['error']){ 
        $resl['error'] = $response['error']['details'][0]['message'].' AND  '.$response['error']['details'][1]['message'] ;
      }else{
         
        $resl['tracking_number'] = $response['tracking_number'];
      }
     
  }

  

    return $resl;
}
}

    /**
     * 打印面单
     * @param $order_id
     * @param $lang  
     * @return mixed
     */
    public function routePrint($order_id )
    {
        $order = WmsSoBill::findOne($order_id);
        $trackingID = $order->lc_number;
        $bill_url = $this->ninjavan_site.$this->country_code."/2.0/reports/waybill?tids=";
        $bearerToken = $this->accessToken();

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $bill_url.$trackingID,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer ".$bearerToken
          )
      ));

      $response = curl_exec($curl); // result from the request
      $err = curl_error($curl);

      curl_close($curl);

      if($err){
        $resl['error'] = "CURL ERROR:" . $err;

      }else{
            $response = json_decode( $response,1);
         if($response['error']){ 
            $resl['error'] = $response['error']['details'][0]['message']  ;
          }else{
             
            $resl['print'] = $response['XXX']; //TODO 不知道返回什么真实值
          }
      }


    }

    


 
}