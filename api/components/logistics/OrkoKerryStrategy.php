<?php

namespace api\components\logistics;

use linslin\yii2\curl;

/**
 * OrkoKerry 物流
 */
class OrkoKerryStrategy implements LogisticsStrategy
{
    const IS_TEST = false; 

    // 请求地址
    private $url;

    // 应用id
    private $appId;

    // 应用key
    private $appKey;

    public function __construct()
    {
        if (self::IS_TEST) {
            $this->url = 'http://exch.th.kerryexpress.com/ediwebapi_uat/SmartEDI/shipment_info';
            $this->appId = 'ANKEY';
            $this->appKey = '0b46d595-14b0-49ac-b901-89872a901ea7';
        } else {
            $this->url = 'http://exch.th.kerryexpress.com/ediwebapi/SmartEDI/shipment_info';
            $this->appId = 'ANKEY';
            $this->appKey = 'bc0c7c95-c199-4490-82ac-8d667820ffbb';
        }
    }

    public function push($data)
    {
        $reqData = [
            'req' => [
                'shipment' => [
                    'con_no' => 'ANKE'.(time() - 1000000000),
                    's_name' => 'Ankey International Logistics Co.,Ltd.',
                    's_address' => '333 Silom road Bangkok 10500 Thailand',
                    's_zipcode' => '10500',
                    's_mobile1' => '6625088418',
                    's_contact' => 'Ankey',
                    'r_name' => $data['name'],
                    'r_address' => $data['address'],
                    'r_subdistrict' => $data['area'],
                    'r_district' => $data['district'],
                    'r_province' => $data['city'],
                    'r_zipcode' => $data['post_code'],
                    'r_mobile1' => $data['mobile'],
                    'r_email' => $data['email'],
                    'r_contact' => $data['name'],
                    'special_note' => $data['comment'],
                    'service_code' => 'ND',
                    'cod_amount' => $data['price'],
                    'cod_type' => 'THB',
                    'tot_pkg' => 1,
                    'declare_value' => $data['declaration_price'],
                    'ref_no' => $data['id'],
                    'action_code' => 'A',
                ],
            ],
        ];
        // 实例化curl 发起请求
        $curl = new curl\Curl();
        $response = $curl->setPostParams($reqData)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'app_id' => $this->appId,
                'app_key' => $this->appKey,
            ])
            ->post($this->url);

        $res = json_decode($response, true);
        if ($res['res']['shipment']['status_code'] == '00') {
            // todo 记录日志 提示什么的
            return true;
        } else {
            // todo 记录日志 提示什么的
            return false;
        }
    }
}
