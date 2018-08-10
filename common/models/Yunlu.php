<?php
/**
 * 云路订单系统
 * Created by Joe han.
 * User: pc
 * Date: 2018/2/1
 * Time: 15:13
 * http://vip.yl-scm.com
 * 账号：YL00001001 密码：123456
 * http://vip.yl-scm.com:22221/vip-customer-web/login.do
 * eccompanyid=AOKE
 * key=03f632f910372034fcf1fae153c7cf5b
 * 正式地址：
 * 下单接口：http://api.yl-scm.com:22220/yunlu-order-web/order/orderAction!createOrder.action
 * customerid=YL00001001
 * 电子面单：http://api.yl-scm.com:22220/yunlu-order-web/rotaPrin/rotaPrintAction!getPrintUrl.action
 * 开发地址：http://developer.yl-scm.com:22222/yunlu-developer-document/
 */

namespace common\models;

use Yii;
use common\models\WmsSoBill; 
use common\models\WmsSoBillDetail; 
use common\models\WmsOrderPackageWz;
use common\models\WmsProduct;

class Yunlu
{
    private $key = 'b9b49fac27e3a89697d65938bdf7eb4e';
    private $eccompanyid = 'AOKEKEJI';//WOWMALL
    private $customerid = 'S00001C005';
    private $create_url = 'http://api.yl-scm.com:22220/yunlu-order-web/order/orderAction!createOrder.action';
    private $print_url = 'http://api.yl-scm.com:22220/yunlu-order-web/rotaPrin/rotaPrintAction!getPrintUrl.action';
    private $track_url = 'http://api.yl-scm.com:22220/yunlu-order-web/track/trackAction!trackForJson.action';
    private $test_track_url = 'http://c196t65309.iok.la:22220/yunlu-order-web/track/trackAction!trackForJson.action';

    /**
     * 下单
     * @param $order_id
     * @return mixed
     */
    public function orderCreate($order_id)
    {
        $order = WmsSoBill::findOne($order_id);
        if ($order) {
            $order_wv = WmsOrderPackageWz::findOne(['order_id' => $order_id]);
            $product = WmsProduct::findOne($order->website);
            $order_items = WmsSoBillDetail::findAll(['order_id' => $order_id]);
            $items = [];
            $en_name = preg_replace('/【.+】/i', '', $product->declaration_ename);
            $qty = 0;
            foreach ($order_items as $v) {
                $qty += $v->qty;
                $items[] = [
                    'itemname' => $product->declaration_cname,
                    'englishName' => ($v->price == 0 ? $en_name.'[赠送]' : $en_name),
                    'number' => $v->qty,
                    'itemvalue' => ($v->price == 0 ? 100 : $v->price), // 价格为0是赠品 需求要改成默认100
                    'itemurl'   => 'http://'.$product->host.'.'.$product->domain.'?q='.$order->id,
                    'itemdesc' => ($v->price == 0 ? '赠送' : ''), 
                ];
            }
            $item_value = $order->price/$qty;
            foreach ($items as $key=>$v)
            {
                $items[$key]['itemvalue'] = $item_value;
            }
            $mobile = preg_replace('/[^\d]/i', '', $order->mobile);
            $logistics_interface = [
                'eccompanyid' => $this->eccompanyid,
                'customerid' => $this->customerid,
                'txlogisticid' => $order->id,
                'mailno' => '',
                'ordertype' => 1,
                'servicetype' => 1,
                'sender' => [
                    'name' => 'AngelTmall',
                    'postcode' => '850001',
                    'mobile' => '852 8009 06019',
                    'prov' => 'GuangDong',
                    'city' => 'DongGuan',
                    'area' => 'Chang An Zhen',
                    'address' => 'Xing Yi Road 205',
                    'mailbox' => 'supportid@angeltmall.com'
                ],
                'receiver' => [
                    'name' => str_replace(['"', "'"], '', $order->name),
                    'postcode' => $order->post_code,
                    'mobile' => $mobile,
                    'prov' => $order->city,
                    'city' => $order->district,
                    'area' => $order->area,
                    'address' => $order->address,
                ],
                'createordertime' => date('Y-m-d H:i:s'),
                'sendstarttime' => date('Y-m-d H:i:s'),
                'sendendtime' => date('Y-m-d H:i:s'),
                'paytype' => '1',
                'weight' => $order_wv->weight,
                'totalquantity' => $order->qty,
                'itemsvalue' => $order->price,
                'remark' => $order->comment,
                'items' => $items
            ];
            $logistics_interface = json_encode($logistics_interface);
            $data_digest = base64_encode(md5($logistics_interface . $this->key));
            $data = [
                'eccompanyid' => $this->eccompanyid,
                'msg_type' => 'ORDERCREATE',
                'logistics_interface' => $logistics_interface,
                'data_digest' => $data_digest
            ];
//            print_r($data);

            return $this->curlPost($data, $this->create_url);
        }
    }

    /**
     * 打印面单
     * @param $order_id
     * @param $lang 面单语言 id=印尼文    en=英文
     * @return mixed
     */
    public function routePrint($order_id, $lang = 'id')
    {
        $order = WmsSoBill::findOne($order_id);
        $logistics_interface = [
            'billcode' => $order->lc_number,
            'lang' => $lang
        ];
        $logistics_interface = json_encode($logistics_interface);
        $data_digest = base64_encode(md5($logistics_interface . $this->key));

        $data = [
            'eccompanyid' => $this->eccompanyid,
            'msg_type' => 'ROTAPRINT',
            'logistics_interface' => $logistics_interface,
            'data_digest' => $data_digest
        ];
        return $this->curlPost($data, $this->print_url);
    }

    /**
     * 货态查询
     * @param $track_number
     * @param string $lang
     * @return mixed
     */
    public function track($track_number, $lang = 'zh')
    {
        $logistics_interface = [
            'billcode' => $track_number,
            'lang' => $lang
        ];
        $logistics_interface = json_encode($logistics_interface);
        $data_digest = base64_encode(md5($logistics_interface . $this->key));

        $data = [
            'eccompanyid' => $this->eccompanyid,
            'msg_type' => 'TRACKQUERY',
            'logistics_interface' => $logistics_interface,
            'data_digest' => $data_digest
        ];

        return $this->curlPost($data, $this->track_url);
    }

    /**
     * 从JET网站获取货态
     * @param $track_number
     */
    public function trackJt($track_number)
    {
        $url = 'http://jk.jet.co.id:22232/jandt_web/shopee/trackingAction!tracking.action';
        $data = json_encode(['awb' => $track_number]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        $json_data = curl_exec($ch);
        curl_close($ch);
//        echo 'J&T Return';
//        print_r($json_data);
        return json_decode($json_data);
    }

    private function curlPost($data, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $json_data = curl_exec($ch);
        curl_close($ch);
        return json_decode($json_data);
    }

}
