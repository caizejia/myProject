<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_finance_freight_formula".
 *
 * @property integer $id
 * @property string $country
 * @property integer $remote
 * @property string $lc
 * @property string $lw
 * @property integer $type
 * @property string $first_weight_t
 * @property string $continued_weight_t
 * @property string $freight
 * @property string $change_the_bill
 * @property string $first_weight_w
 * @property string $continued_weight_w
 * @property integer $billing_method
 * @property integer $user_id
 * @property string $create_date
 */
class WmsFinanceFreightFormula extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_finance_freight_formula';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country', 'lc', 'lw', 'billing_method', 'user_id'], 'required'],
            [['remote', 'type', 'billing_method', 'user_id'], 'integer'],
            [['create_date'], 'safe'],
            [['country', 'lc', 'lw', 'first_weight_t', 'continued_weight_t', 'freight', 'change_the_bill', 'first_weight_w', 'continued_weight_w'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country' => 'Country',
            'remote' => 'Remote',
            'lc' => 'Lc',
            'lw' => 'Lw',
            'type' => 'Type',
            'first_weight_t' => 'First Weight T',
            'continued_weight_t' => 'Continued Weight T',
            'freight' => 'Freight',
            'change_the_bill' => 'Change The Bill',
            'first_weight_w' => 'First Weight W',
            'continued_weight_w' => 'Continued Weight W',
            'billing_method' => 'Billing Method',
            'user_id' => 'User ID',
            'create_date' => 'Create Date',
        ];
    }

    /**
     * 计算运费
     * @param $order array 订单信息
     * @param null $cod_sxf
     * @param null $tk_sxf
     * @param $data array  运费计算公式
     * @return array|float|int
     */
    public function actionCalculation($order,$data,$cod_sxf=null,$tk_sxf=null){
        if(!is_null($cod_sxf)){
            $order = array_merge($order,[ 'cod_sxf' => true ]);
        }
        if(!is_null($tk_sxf)){
            $order = array_merge($order,[ 'tk_sxf' => true ]);
        }

        switch ($order['lc']){
            case '商壹':
                $order['country'] = $this->countryMy($order['id']);  //MY 处理分为东马 MY1 和  西马  MY0
                $res = $this->shangyi($order,$data);
                break;
            case '易速配':
                $res = $this->yisupei($order,$data);
                break;
            case '壹加壹':
                $res = $this->yjy($order,$data);
                break;
            case '泰好':
                $res = $this->th($order,$data);
                break;
            case '博佳图':
                $res = $this->bjt($order,$data);
                break;
            case '森鸿':
                $res = $this->sh($order,$data);
                break;
            case 'K1':
                $order['country'] = $this->countryMy($order['id']);  //MY 处理分为东马 MY1 和  西马  MY0
                $res = $this->k1($order,$data);
                break;
            case 'imile':
                $res = $this->imile($order,$data);
                break;
            case '合联':
                $res = $this->hl($order,$data);
                break;
            case '和洋':
                $res = $this->hy($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 和洋 运费计算
     * @param $order
     * @return mixed
     */
    public function hy($order,$data){
        switch ($order['country']){
            case 'PHL':
                $res = $this->hyPhl($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 合联 运费计算
     * @param $order
     * @return mixed
     */
    public function hl($order,$data){
        switch ($order['country']){
            case 'UAE':
                $res = $this->hlUae($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * imile 运费计算
     * @param $order
     * @return mixed
     */
    public function imile($order,$data){
        switch ($order['country']){
            case 'UAE':
                $res = $this->imileUae($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * K1 运费计算
     * @param $order
     * @return mixed
     */
    public function k1($order,$data){
        switch ($order['country']){
            case 'MY0':
                $res = $this->k1My0($order,$data);
                break;
            case 'MY1':
                $res = $this->k1My1($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 森鸿 运费计算
     * @param $order
     * @return mixed
     */
    public function sh($order,$data){
        switch ($order['country']){
            case 'TW':
                $res = $this->shTw($order,$data);
                break;
            case 'HK':
                $res = $this->shHk($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 博佳图 运费计算
     * @param $order
     * @return mixed
     */
    public function bjt($order,$data){
        switch ($order['country']){
            case 'TH':
                $res = $this->bjtTh($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 泰好 运费计算
     * @param $order
     * @return mixed
     */
    public function th($order,$data){
        switch ($order['country']){
            case 'TH':
                $res = $this->thTh($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 壹加壹 运费计算
     * @param $order
     * @return mixed
     */
    public function yjy($order,$data){
        switch ($order['country']){
            case 'TW':
                $res = $this->yjyTw($order,$data);
                break;
            case 'HK':
                $res = $this->yjyHk($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 易速配 运费计算
     * @param $order
     * @return mixed
     */
    public function yisupei($order,$data){
        switch ($order['country']){
            case 'TW':
                $res = $this->yisupeiTw($order,$data);
                break;
            case 'HK':
                $res = $this->yisupeiHk($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 商壹 运费计算
     * @param $order
     * @return array|float|int
     */
    public function shangyi($order,$data){
        switch ($order['country']){
            case 'SG':
                $res = $this->shangyiSg($order,$data);
                break;
            case 'MY0':
                $res = $this->shangyiMyX($order,$data);
                break;
            case 'MY1':
                $res = $this->shangyiMyD($order,$data);
                break;
            case 'TH':
                $res = $this->shangyiTh($order,$data);
                break;
            case 'LKA':
                $res = $this->shangyiLka($order,$data);
                break;
            default:
                break;
        }
        return $res;
    }

    /**
     * 商壹 SG 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function shangyiSg($order,$data){
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);   //TODO  写个方法判断是否转运
        //根据普货敏感货使用不同的公式
        $data = $data[$order['lc']][$order['country']][$order['type']];
        if(!$zhuangyun){
            // 不是转运，国内仓发货
            //运费
            $yunfei = $data['运费'] * $order['weight'];
            if($order['weight'] > $data['首重(派送)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(派送)'] + ceil($order['weight'] - $data['首重(派送)']) * $data['续重费用(派送)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = $yunfei + $paisongfei;
        }else{
            //换单费
            $huandan = $data['每票换单费'];
            if($order['weight'] > $data['首重(派送)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(派送)'] + ceil($order['weight'] - $data['首重(派送)']) * $data['续重费用(派送)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = $huandan + $paisongfei;
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = ($cod_sxf_b = $order['COD'] * $data['COD手续费比例']) > $data['COD手续费最低值'] ? $cod_sxf_b : $data['COD手续费最低值'];
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = ($cod_sxf_b = $order['COD'] * $data['退货退款比例']) > $data['退货退款最低值'] ? $cod_sxf_b : $data['退货退款最低值'];
        }

        $res = [ '运费' => $res ];
        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 商壹 MY0 马来西亚（西）运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function shangyiMyX($order,$data){
        $res = $this->shangyiSg($order,$data);
        return $res;
    }

    /**
     * 商壹 MY1 马来西亚（东）运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function shangyiMyD($order,$data){
        $res = $this->shangyiSg($order,$data);
        return $res;
    }

    /**
     * 商壹 TH 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function shangyiTh($order,$data){
        $res = $this->shangyiSg($order,$data);
        return $res;
    }

    /**
     * 商壹 LKA 斯里兰卡 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function shangyiLka($order,$data){
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);     //TODO  写个方法判断是否转运
        //根据普货敏感货使用不同的公式
        $data = $data[$order['lc']][$order['country']][$order['type']];
        if(!$zhuangyun){
            // 不是转运，国内仓发货
            $area = '科伦坡';     //TODO  写个方法判断地区是   科伦坡     其它
            $data = $data[$area];
            if($order['weight'] > $data['首重(派送)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(派送)'] + ceil($order['weight'] - $data['首重(派送)']) * $data['续重费用(派送)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = $paisongfei;
        }else{
            $area = '科伦坡';     //TODO  写个方法判断地区是   科伦坡     其它
            $data = $data[$area];
            //换单费
            $huandan = $data['每票换单费'];
            //派送费
            $paisongfei = $data['每票换单派送费'];
            $res = $huandan + $paisongfei;
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = ($cod_sxf_b = $order['COD'] * $data['COD手续费比例']) > $data['COD手续费最低值'] ? $cod_sxf_b : $data['COD手续费最低值'];
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = ($cod_sxf_b = $order['COD'] * $data['退货退款比例']) > $data['退货退款最低值'] ? $cod_sxf_b : $data['退货退款最低值'];
        }

        $res = [ '运费' => $res ];
        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 易速配 TW 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function yisupeiTw($order,$data){
        //通过订单号 获取包裹信息 长宽高重
        $c = $order['length'];
        $k = $order['width'];
        $g = $order['height'];

        $data = $data[$order['lc']][$order['country']][$order['type']];
        //计算体积重
        $weight_t = sprintf('%.2f', ($c * $k * $g) / $data['体积重计算']);

        //判断实际使用哪个重量
        switch ($data['体积重计算方式']){
            case '平均' :
                $order['weight'] = ($weight_t + $order['weight']) / $order['weight'];
                break;
            case '实重' :
                $order['weight'] = $order['weight'];
                break;
            case '体积重' :
                $order['weight'] = $weight_t;
                break;
            case '谁重用谁' :
                $order['weight'] = $weight_t > $order['weight'] ? $weight_t : $order['weight'];
                break;
            default:
                break;
        }


        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);

        if(!$zhuangyun){
            if($order['weight'] > $data['首重(派送)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(派送)'] + ceil($order['weight'] - $data['首重(派送)']) * $data['续重费用(派送)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = ['运费' => $paisongfei];
        }else{
            //换单费  退件重出
            $huandan = $data['退件重出（台币/票）'] + $data['退件重出费用'];
            $res = ['运费' => $huandan, '运费货币' => 'TWD'];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = 0;
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = $data['客诉打款手续费（台币/票）'];
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 易速配 HK 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function yisupeiHk($order,$data){
        //通过订单号 获取包裹信息 长宽高重
        $c = $order['length'];
        $k = $order['width'];
        $g = $order['height'];

        $data = $data[$order['lc']][$order['country']][$order['type']];
        //计算体积重
        $weight_t = sprintf('%.2f', ($c * $k * $g) / $data['体积重计算']);

        //判断实际使用哪个重量
        switch ($data['体积重计算方式']){
            case '平均' :
                $order['weight'] = ($weight_t + $order['weight']) / $order['weight'];
                break;
            case '实重' :
                $order['weight'] = $order['weight'];
                break;
            case '体积重' :
                $order['weight'] = $weight_t;
                break;
            case '谁重用谁' :
                $order['weight'] = $weight_t > $order['weight'] ? $weight_t : $order['weight'];
                break;
            default:
                break;
        }


        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);

        if(!$zhuangyun){
            if($order['weight'] > $data['首重(派送)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(派送)'] + ceil($order['weight'] - $data['首重(派送)']) * $data['续重费用(派送)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = ['运费' => $paisongfei];
        }else{
            //换单费  退件重出
            $huandan = $data['退件重出'];   //todo  待定价
            $res = ['运费' => $huandan];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = 0;
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $dk = $this->yspHkdk($order['id']);
            $tk_sxf = $data['客诉打款手续费'][$dk];
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 壹加壹 TW 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function yjyTw($order,$data){
        $data = $data[$order['lc']][$order['country']][$order['type']];
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);

        if(!$zhuangyun){
            if($order['weight'] > $data['首重(派送)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(派送)'] + ceil($order['weight'] - $data['首重(派送)']) * $data['续重费用(派送)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = ['运费' => $paisongfei];
        }else{
            //换单费  退件重出
            $huandan = $data['转寄费（含人工包装费25）（台币）'];
            $res = ['运费' => $huandan,'运费货币' => 'TWD'];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = 0;
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = $data['退款费（台币）'] + $data['上门取件费（台币）'];
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf ,'退款手续费货币' => 'TWD']);
        }

        return $res;
    }

    /**
 * 壹加壹 HK 运费计算
 * @param $order
 * @return array|float|int
 * @throws \yii\db\Exception
 */
    public function yjyHk($order,$data){
        $data = $data[$order['lc']][$order['country']];
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);

        if(!$zhuangyun){
            if($order['weight'] > $data['首重(派送)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(派送)'] + ceil($order['weight'] - $data['首重(派送)']) * $data['续重费用(派送)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = ['运费' => $paisongfei];
        }else{
            //换单费  退件重出
            $huandan = $data['转寄费（港币）'];
            $res = ['运费' => $huandan,'运费货币' => 'HKD'];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = 0;
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = 0;
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf ]);
        }

        return $res;
    }

    /**
     * 泰好 TH 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function thTh($order,$data){
        $area = $this->thArea($order['id']);
        $data = $data[$order['lc']][$order['country']][$area][$order['type']];
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);

        if(!$zhuangyun){
            if($order['weight'] <= 3){  //重量小于首重
                //派送费
                $paisongfei = $data['首重费用(1-3)'] + ceil($order['weight'] - $data['首重(1-3)']) * $data['续重费用(1-3)'];
            } elseif($order['weight'] > 3 && $order['weight'] <= 5){
                //派送费
                $paisongfei = $data['续重费用(3.01-5)'] * $data['weight'];
            }elseif($order['weight'] > 5 && $order['weight'] <= 10){
                //派送费
                $paisongfei = $data['续重费用(5.01-10)'] * $data['weight'];
            }
            $res = ['运费' => $paisongfei];
        }else{
            //换单费  退件重出
            $huandan = $data['转寄操作费'];
            if($order['weight'] <= $data['拒收退回仓库费（首重）']){
                $paisongfei = $data['拒收退回仓库费（首重费用）'];
            }else{
                $paisongfei = $data['拒收退回仓库费（首重费用）'] + ceil($order['weight'] - $data['拒收退回仓库费（首重）']) * $data['拒收退回仓库费（续重费用）'];
            }
            $res = ['运费' => $paisongfei + $huandan];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = 15;
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = 0;
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf ]);
        }

        return $res;
    }

    /**
     * 博佳图 TH 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function bjtTh($order,$data){
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);     //TODO  写个方法判断是否转运
        //根据普货敏感货使用不同的公式
        $data = $data[$order['lc']][$order['country']];
        $type = $this->bjtThType($order['id']);
        $data = $data[$type];
        $area = $this->bjtThArea($order['id']);
        if(!$zhuangyun){
            // 不是转运，国内仓发货
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用'] + ceil($order['weight'] - $data['首重']) * $data['续重费用'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(派送)'];
            }
            $res = $paisongfei;
        }else{
            $data_area = $data['当地转'][$area];   //TODO  写个方法判断地区是          BKK   其它城市   特别偏远

            //换单费
            $huandan = $data['转寄打单费'];
            //派送费
            if($order['weight'] > $data_area['当地转派送费首重']){  //重量大于首重
                //派送费
                $paisongfei = $data_area['当地转派送费首重费用'] + ceil($order['weight'] - $data_area['当地转派送费首重']) * $data_area['当地转派送费续重费用'];
            } else{
                //派送费
                $paisongfei = $data_area['当地转派送费首重费用'];
            }
            $res = $huandan + $paisongfei;
        }

        if($area == '特别偏远'){
            $res += 40;
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            if($order['COD'] > 10000){
                $cod_sxf = $order['COD'] * $data['COD手续费比例（超过10000TH）'];
            }else{
                $cod_sxf = $data['COD手续费'];
            }
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = 0;
        }

        $res = [ '运费' => $res ];
        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 森鸿 TW 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function shTw($order,$data){
        $data = $data[$order['lc']][$order['country']];
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);

        if(!$zhuangyun){
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(台币)'] + ceil($order['weight'] - $data['首重']) * $data['续重费用(台币)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(台币)'];
            }
            $res = ['运费' => $paisongfei ,'运费货币' => 'TWD'];
        }else{
            //换单费  退件重出
            $huandan = $data['转寄或重出仓储理货费(台币)'] + $data['转寄或重出黑猫派送费(台币)'] + $data['转寄或重出代收手续费(台币)'];
            $res = ['运费' => $huandan];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = 0;
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            if($order['COD'] <= 5000){
                $tk_sxf = 0;
            }else{
                $tk_sxf = 30;
            }
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf , 'COD手续费货币' => 'TWD']);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 森鸿 HK 运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function shHk($order,$data){
        //通过订单号 获取包裹信息 长宽高重
        $c = $order['length'];
        $k = $order['width'];
        $g = $order['height'];

        $data = $data[$order['lc']][$order['country']];
        //计算体积重
        $weight_t = sprintf('%.2f', ($c * $k * $g) / $data['体积重计算']);

        //判断实际使用哪个重量
        switch ($data['体积重计算方式']){
            case '平均' :
                $order['weight'] = ($weight_t + $order['weight']) / $order['weight'];
                break;
            case '实重' :
                $order['weight'] = $order['weight'];
                break;
            case '体积重' :
                $order['weight'] = $weight_t;
                break;
            case '谁重用谁' :
                $order['weight'] = $weight_t > $order['weight'] ? $weight_t : $order['weight'];
                break;
            default:
                break;
        }


        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);

        if(!$zhuangyun){
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用'] + ceil($order['weight'] - $data['首重']) * $data['续重费用'];
            } else{
                //派送费
                $paisongfei = $data['首重费用'];
            }
            $res = ['运费' => $paisongfei ];
        }else{
            //换单费  退件重出
            $huandan = $data['仓储理货费'];
            if($order['weight'] > $data['首重(重出)']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用(重出)'] + ceil($order['weight'] - $data['首重(重出)']) * $data['续重费用(重出)'];
            } else{
                //派送费
                $paisongfei = $data['首重费用(重出)'];
            }
            $res = ['运费' => $huandan + $paisongfei];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = 0;
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = 0;
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf , 'COD手续费货币' => 'TWD']);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * K1 MY0  西马   运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function k1My0($order,$data){
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);     //TODO  写个方法判断是否转运
        //根据普货敏感货使用不同的公式
        $data = $data[$order['lc']][$order['country']][$order['type']];
        if(!$zhuangyun){
            // 不是转运，国内仓发货
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用'] + ceil($order['weight'] - $data['首重']) * $data['续重费用'];
            } else{
                //派送费
                $paisongfei = $data['首重费用'];
            }
            $res = $paisongfei;
        }else{
            //换单费
            $huandan = $data['换单费'];
            //派送费
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['换单首重费用'] + ceil($order['weight'] - $data['首重']) * $data['换单续重费用'];
            } else{
                //派送费
                $paisongfei = $data['换单首重费用'];
            }
            $res = $huandan + $paisongfei;
        }


        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = ($cod_sxf_b = $order['COD'] * $data['COD手续费比例']) > $data['COD手续费最低值'] ? $cod_sxf_b : $data['COD手续费最低值'];
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = $data['拒签退回费用'];
        }

        $res = [ '运费' => $res ];
        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * K1 MY1  东马   运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function k1My1($order,$data){
        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);     //TODO  写个方法判断是否转运
        //根据普货敏感货使用不同的公式
        $data = $data[$order['lc']][$order['country']][$order['type']];
        if(!$zhuangyun){
            // 不是转运，国内仓发货
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用'] + ceil($order['weight'] - $data['首重']) * $data['续重费用'];
            } else{
                //派送费
                $paisongfei = $data['首重费用'];
            }
            $res = $paisongfei;
        }else{
            //换单费
            $huandan = $data['换单费'];
            //派送费
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['换单首重费用'] + ceil($order['weight'] - $data['首重']) * $data['换单续重费用'];
            } else{
                //派送费
                $paisongfei = $data['换单首重费用'];
            }
            $res = $huandan + $paisongfei;
        }


        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = ($cod_sxf_b = $order['COD'] * $data['COD手续费比例']) > $data['COD手续费最低值'] ? $cod_sxf_b : $data['COD手续费最低值'];
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = $data['拒签退回费用'];
        }

        $res = [ '运费' => $res ];
        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * imile UAE    运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function imileUae($order,$data){
        //获取地区
        $area = $this->imileArea($order['id']);
        //获取类型
        $order['type'] = $this->imileType($order['id']);
        //根据普货敏感货使用不同的公式
        $data = $data[$order['lc']][$order['country']][$area][$order['type']];
        if($order['weight'] <= $data['派送首重']){  //重量大于首重
            //派送费
            $paisongfei = $data['派送首重费用(AED)'];
        } else{
            //派送费
            $paisongfei = $data['派送首重费用(AED)'] + ceil($order['weight'] - $data['派送首重']) * $data['派送续重费用(AED)'];
        }
        $res = $paisongfei;


        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = $order['COD'] * $data['代收货款服务费比例'];
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = 0;
        }

        $res = [ '运费' => $res , '运费货币' => 'AED'];
        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 合联 UAE  运费计算
     * @param $order
     * @param $data
     * @return array
     */
    public function hlUae($order,$data){
        $data = $data[$order['lc']][$order['country']];
        $order['weight'] = ceil($order['weight'] * 10);
        if($order['weight'] > 40){
            $res = ['运费' => 141 ];
        }elseif($order['weight'] < 1){
            $res = ['运费' => 27 ];
        }else{
            $res = ['运费' => $data['运费'][$order['weight']] ];
        }

        //COD 手续费
        if(isset($order['cod_sxf'])){
            if($order['COD'] <= 138){
                $cod_sxf = $data['COD手续费($)'][1];
            }elseif($order['COD'] > 138 && $order['COD'] <= 273){
                $cod_sxf = $data['COD手续费($)'][138];
            }elseif($order['COD'] > 273 && $order['COD'] <= 410){
                $cod_sxf = $data['COD手续费($)'][273];
            }elseif($order['COD'] > 410 && $order['COD'] <= 1366){
                $cod_sxf = $data['COD手续费($)'][410];
            }else{
                $cod_sxf = $data['COD手续费($)'][1366];
            }
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = 0;
        }

        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf ,'COD手续费货币' => '$']);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }

    /**
     * 和洋 PHL    运费计算
     * @param $order
     * @return array|float|int
     * @throws \yii\db\Exception
     */
    public function hyPhl($order,$data){
        //通过订单号 获取包裹信息 长宽高重
        $c = $order['length'];
        $k = $order['width'];
        $g = $order['height'];

        $data = $data[$order['lc']][$order['country']];
        //计算体积重
        $weight_t = sprintf('%.2f', ($c * $k * $g) / $data['体积重计算']);

        //判断实际使用哪个重量
        switch ($data['体积重计算方式']){
            case '平均' :
                $order['weight'] = ($weight_t + $order['weight']) / $order['weight'];
                break;
            case '实重' :
                $order['weight'] = $order['weight'];
                break;
            case '体积重' :
                $order['weight'] = $weight_t;
                break;
            case '谁重用谁' :
                $order['weight'] = $weight_t > $order['weight'] ? $weight_t : $order['weight'];
                break;
            default:
                break;
        }

        //通过订单号判断该订单是否由另一个订单转运过来；
        //1.是 为 true （海外仓发货）
        //2.不是为 false  （国内仓发货）
        $zhuangyun = $this->zhuanyun($order['id']);     //TODO  写个方法判断是否转运
        if(!$zhuangyun){
            // 不是转运，国内仓发货
            if($order['weight'] > $data['首重']){  //重量大于首重
                //派送费
                $paisongfei = $data['首重费用'] + (ceil(($order['weight'] - $data['首重']) * 2 ))/2 * $data['续重费用'];
            } else{
                //派送费
                $paisongfei = $data['首重费用'];
            }
            $res = $paisongfei;
        }else{
            //换单费
            $huandan = $data['改派或重发费用'];

            $res = $huandan;
        }


        //COD 手续费
        if(isset($order['cod_sxf'])){
            $cod_sxf = ($cod_sxf_b = $order['COD'] * $data['代收手续费比例']);
        }
        //退款手续费
        if(isset($order['tk_sxf'])) {
            $tk_sxf = $data['退款手续费比例'];
        }

        $res = [ '运费' => $res ];
        if(isset($cod_sxf)){
            $res = array_merge($res,[ 'COD手续费' => $cod_sxf]);
        }
        if(isset($tk_sxf)){
            $res = array_merge($res,[ '退款手续费' => $tk_sxf]);
        }

        return $res;
    }












    /**
     * MY 处理分为东马 MY1 和  西马  MY0
     * @param $id
     * @return string  返回二选一   MY0   MY1
     */
    public function countryMy($id){
        return 'MY1';   //MY 处理分为东马 MY1 和  西马  MY0
    }

    /**
     * imile   UAE  判断货物类型
     * @param $id
     * @return string   三选一    P    M   特殊货
     */
    public function imileType($id){
        return 'P';  //P    M   特殊货
    }

    /**
     * imile   UAE  判断派送区域
     * @param $id
     * @return string   三选一    迪拜    沙迦   阿联酋其他地区
     */
    public function imileArea($id){
        return '阿联酋其他地区';  //迪拜    沙迦   阿联酋其他地区
    }

    /**
     * 博佳图   TH  判断派送区域
     * @param $id
     * @return string   三选一    BKK    其它城市   特别偏远
     */
    public function bjtThArea($id){
        return '特别偏远';  //BKK    其它城市   特别偏远
    }

    /**
     * 博佳图   TH  判断物品种类
     * @param $id
     * @return string    三选一     P    M   化妆品类
     */
    public function bjtThType($id){
        return 'P';   //P    M   化妆品类
    }

    /**
     * 判断是否转运   TODO
     * @param $id
     * @return bool
     */
    public function zhuanyun($id){
        return true;
    }

    /**
     * 易速配  香港  客户打款方式
     * @param $id
     * @return string
     */
    public function yspHkdk($id){
        return '恒生汇丰';    //恒生汇丰    其它
    }

    /**
     * 泰好 TH 判断派送区域
     * @param $id
     * @return string
     */
    public function thArea($id){
        return '曼谷市区'; //曼谷市区
    }

    public function calculation(){
        $data = [
            '商壹' => [
                'SG' => [
                    'P' => [
                        '首重(派送)' => 3,
                        '首重费用(派送)' => 30,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 5,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 17,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 15,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 15,
                        '包裹限重' => 30,
                        '全程时效' => '3-5',
                        '渠道代码' => [
                            'NJV服务' => 'ECOM-NJV-P',
                        ],
                    ],
                    'M' => [
                        '首重(派送)' => 3,
                        '首重费用(派送)' => 30,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 5,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 19,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 15,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 15,
                        '包裹限重' => 30,
                        '全程时效' => '3-5',
                        '渠道代码' => [
                            'NJV服务' => 'ECOM-NJV-M',
                        ],
                    ],
                ],
                //东马
                'MY1' => [
                    'P' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 27,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 26,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 15,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 10,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 10,
                        '包裹限重' => 30,
                        '全程时效' => '5-7',
                        '渠道代码' => [
                            'DHL服务' => 'ECOM-GMS-P',
                            'GDEX服务' => 'ECOM-COD-GP',
                        ],
                    ],
                    'M' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 27,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 26,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 16.5,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 10,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 10,
                        '包裹限重' => 30,
                        '全程时效' => '5-7',
                        '渠道代码' => [
                            'DHL服务' => 'ECOM-GMS-DM',
                            'GDEX服务' => 'ECOM-COD-GD',
                        ],
                    ],
                ],
                //西马
                'MY0' => [
                    'P' => [
                        '首重(派送)' => 3,
                        '首重费用(派送)' => 17,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 3.5,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 15,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 10,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 10,
                        '包裹限重' => 30,
                        '全程时效' => '3-5',
                        '渠道代码' => [
                            'DHL服务' => 'ECOM-GMS-P',
                            'GDEX服务' => 'ECOM-COD-GP',
                        ],
                    ],
                    'M' => [
                        '首重(派送)' => 3,
                        '首重费用(派送)' => 17,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 3.5,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 16.5,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 10,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 15,
                        '包裹限重' => 30,
                        '全程时效' => '3-5',
                        '渠道代码' => [
                            'DHL服务' => 'ECOM-GMS-DM',
                            'GDEX服务' => 'ECOM-COD-GD',
                        ],
                    ],
                ],
                'TH' => [
                    'P' => [
                        '首重(派送)' => 3,
                        '首重费用(派送)' => 22,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 3,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 12,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 15,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 10,
                        '包裹限重' => 20,
                        '全程时效' => '5-7',
                        '渠道代码' => [
                            'KERRY服务' => 'ECOM-TH-P',
                        ],
                    ],
                    'M' => [
                        '首重(派送)' => 3,
                        '首重费用(派送)' => 22,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 3,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '运费' => 15,
                        'COD手续费比例' => 0.03,
                        'COD手续费最低值' => 15,
                        '每票换单费' => 5,
                        '退货退款比例' => 0.03,
                        '退货退款最低值' => 10,
                        '包裹限重' => 20,
                        '全程时效' => '7-9',
                        '渠道代码' => [
                            'KERRY服务' => 'ECOM-TH-M',
                        ],
                    ],
                ],
                'LKA' => [
                    'P' => [
                        '科伦坡' => [
                            '首重(派送)' => 0.5,
                            '首重费用(派送)' => 45,
                            '续重(派送)' => 0.5,
                            '续重费用(派送)' => 18,
                            '首重(头程)' => 0,
                            '首重费用(头程)' => 0,
                            '续重(头程)' => 0,
                            '续重费用(头程)' => 0,
                            'COD手续费比例' => 0.03,
                            'COD手续费最低值' => 20,
                            '每票换单费' => 5,
                            '每票换单派送费' => 18,
                            '退货退款比例' => 0.03,
                            '退货退款最低值' => 20,
                            '包裹限重' => 30,
                            '包裹限重次数' => '免费派送三次',
                            '全程时效' => '3-5',
                        ],
                        '其它' => [
                            '首重(派送)' => 0.5,
                            '首重费用(派送)' => 45,
                            '续重(派送)' => 0.5,
                            '续重费用(派送)' => 18,
                            '首重(头程)' => 0,
                            '首重费用(头程)' => 0,
                            '续重(头程)' => 0,
                            '续重费用(头程)' => 0,
                            'COD手续费比例' => 0.03,
                            'COD手续费最低值' => 20,
                            '每票换单费' => 5,
                            '每票换单派送费' => 25,
                            '退货退款比例' => 0.03,
                            '退货退款最低值' => 20,
                            '包裹限重' => 30,
                            '包裹限重次数' => '免费派送三次',
                            '全程时效' => '3-5',
                        ],
                    ],
                    'M' => [
                        '科伦坡' => [
                            '首重(派送)' => 0.5,
                            '首重费用(派送)' => 65,
                            '续重(派送)' => 0.5,
                            '续重费用(派送)' => 22,
                            '首重(头程)' => 0,
                            '首重费用(头程)' => 0,
                            '续重(头程)' => 0,
                            '续重费用(头程)' => 0,
                            'COD手续费比例' => 0.03,
                            'COD手续费最低值' => 20,
                            '每票换单费' => 5,
                            '每票换单派送费' => 18,
                            '退货退款比例' => 0.03,
                            '退货退款最低值' => 20,
                            '包裹限重' => 30,
                            '包裹限重次数' => '免费派送三次',
                            '全程时效' => '3-5',
                        ],
                        '其它' => [
                            '首重(派送)' => 0.5,
                            '首重费用(派送)' => 65,
                            '续重(派送)' => 0.5,
                            '续重费用(派送)' => 22,
                            '首重(头程)' => 0,
                            '首重费用(头程)' => 0,
                            '续重(头程)' => 0,
                            '续重费用(头程)' => 0,
                            'COD手续费比例' => 0.03,
                            'COD手续费最低值' => 20,
                            '每票换单费' => 5,
                            '每票换单派送费' => 25,
                            '退货退款比例' => 0.03,
                            '退货退款最低值' => 20,
                            '包裹限重' => 30,
                            '包裹限重次数' => '免费派送三次',
                            '全程时效' => '3-5',
                        ],
                    ],
                ],
            ],
            '易速配' => [
                'TW' => [
                    'P' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 30,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 11,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '拒签退回费用' => 0,
                        '签收退货取件（台币/票）' => 85,
                        '退件重出（台币/票）' => 115,
                        '客诉打款手续费（台币/票）' => 30,
                        '体积重计算' => 6000,
                        '体积重计算方式' => '平均', //实重   体积重    谁重用谁   平均
                        '全程时效' => '3-4',
                        '渠道代码' => '黑猫宅急便/邮政/新竹',
                        '退件免费存放天数' => 30,
                        '退件重出费用' => 30,
                    ],
                    'M' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 40,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 20,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '拒签退回费用' => 0,
                        '签收退货取件（台币/票）' => 85,
                        '退件重出（台币/票）' => 115,
                        '客诉打款手续费（台币/票）' => 30,
                        '体积重计算' => 6000,
                        '体积重计算方式' => '平均', //实重   体积重    谁重用谁   平均
                        '全程时效' => '3-4',
                        '渠道代码' => '黑猫宅急便/邮政/新竹',
                        '退件免费存放天数' => 30,
                        '退件重出费用' => 30,
                    ],
                ],
                'HK' => [
                    'P' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 40,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 8,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '拒收退件' => 25,
                        '签收退货取件' => 25,
                        '退件重出' => 0, //todo 不是0，待定价，暂未开通
                        '客诉打款手续费' => [
                            '恒生汇丰' => 20,
                            '其它' => 80,
                        ],
                        '体积重计算' => 6000,
                        '体积重计算方式' => '谁重用谁', //实重   体积重    谁重用谁   平均
                        '全程时效' => '2-3',
                        '渠道代码' => '顺丰',
                    ],
                    'M' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 65,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 25,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '拒收退件' => 25,
                        '签收退货取件' => 25,
                        '退件重出' => 0, //todo 不是0，待定价，暂未开通
                        '客诉打款手续费' => [
                            '恒生汇丰' => 20,
                            '其它' => 80,
                        ],
                        '体积重计算' => 6000,
                        '体积重计算方式' => '谁重用谁', //实重   体积重    谁重用谁   平均
                        '全程时效' => '3-4',
                        '渠道代码' => 'YSP',
                    ],
                ],
            ],
            '壹加壹' => [
                'TW' => [
                    'P' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 30,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 11,
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '退款费（台币）' => 30,
                        '上门取件费（台币）' => 80,
                        '入仓费（免费15天）' => 0,
                        '入仓费（25台币/天*月）' => 5,
                        '转寄费（含人工包装费25）（台币）' => 125,
                        '货物丢失赔偿' => 0.7,
                        '仓储费（免费30天）' => 0,
                        '仓储费（25台币/天*月）' => 30,
                        '仓储费（台币/天*月）' => 25,
                    ],
                    'M' => [
                        '首重(派送)' => 1,
                        '首重费用(派送)' => 40,
                        '续重(派送)' => 1,
                        '续重费用(派送)' => 0, //TODO  不是0 是为空
                        '首重(头程)' => 0,
                        '首重费用(头程)' => 0,
                        '续重(头程)' => 0,
                        '续重费用(头程)' => 0,
                        '退款费（台币）' => 30,
                        '上门取件费（台币）' => 80,
                        '入仓费（免费15天）' => 0,
                        '入仓费（25台币/天*月）' => 5,
                        '转寄费（含人工包装费25）（台币）' => 125,
                        '货物丢失赔偿' => 0.7,
                        '仓储费（免费30天）' => 0,
                        '仓储费（25台币/天*月）' => 30,
                        '仓储费（台币/天*月）' => 25,
                    ],
                    '外岛' => [
                        '首重费用(派送)' => 50,
                    ],
                ],
                'HK' => [
                    '首重(派送)' => 3,
                    '首重费用(派送)' => 42,
                    '续重(派送)' => 1,
                    '续重费用(派送)' => 6,
                    '退货（港币）' => 36,
                    '转寄费（港币）' => 36,
                    '入仓费（免费15天）' => 1.5,
                    '入仓费（0.3港币/天*月）' => 0.3,
                    '货物丢失赔偿' => 0.6,
                ],
            ],
            '泰好' => [
                'TH' => [
                    '曼谷市区' => [
                        'P' => [
                            '首重(1-3)' => 1,
                            '首重费用(1-3)' => 33,
                            '续重(1-3)' => 1,
                            '续重费用(1-3)' => 12,
                            '续重(3.01-5)' => 1,
                            '续重费用(3.01-5)' => 18,
                            '续重(5.01-10)' => 1,
                            '续重费用(5.01-10)' => 17.5,
                            '拒收退回仓库费（首重）' => 1,
                            '拒收退回仓库费（首重费用）' => 18,
                            '拒收退回仓库费（续重）' => 1,
                            '拒收退回仓库费（续重费用）' => 4,
                            '转寄操作费' => 5,
                        ],
                        'M' => [
                            '首重(1-3)' => 1,
                            '首重费用(1-3)' => 36,
                            '续重(1-3)' => 1,
                            '续重费用(1-3)' => 12,
                            '续重(3.01-5)' => 1,
                            '续重费用(3.01-5)' => 18.5,
                            '续重(5.01-10)' => 1,
                            '续重费用(5.01-10)' => 18,
                            '拒收退回仓库费（首重）' => 1,
                            '拒收退回仓库费（首重费用）' => 18,
                            '拒收退回仓库费（续重）' => 1,
                            '拒收退回仓库费（续重费用）' => 4,
                            '转寄操作费' => 5,
                        ],
                    ],
                ],
            ],
            '博佳图' => [
                'TH' => [
                    'P' => [
                        '首重' => 1,
                        '首重费用' => 35,
                        '续重' => 1,
                        '续重费用' => 10,
                        'COD手续费' => 15,
                        'COD手续费比例（超过10000TH）' => 0.015,
                        '周六、早上派送（票）' => 20,
                        '拒收退回仓库费（首重）' => 2,
                        '拒收退回仓库费（首重费用）' => 19,
                        '拒收退回仓库费（续重）' => 1,
                        '拒收退回仓库费（续重费用）' => 3,
                        '仓储费（免费30天）' => 0,
                        '仓储费（0.5元/天*票）' => 0.5,
                        '转寄打单费' => 5,
                        '当地转' => [
                            'BKK' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                            ],
                            '其它城市' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                            ],
                            '特别偏远' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                                '加收费用' => 40,
                            ],
                        ],
                    ],
                    'M' => [
                        '首重' => 1,
                        '首重费用' => 37,
                        '续重' => 1,
                        '续重费用' => 10,
                        'COD手续费' => 15,
                        'COD手续费比例（超过10000TH）' => 0.015,
                        '周六、早上派送（票）' => 20,
                        '拒收退回仓库费（首重）' => 2,
                        '拒收退回仓库费（首重费用）' => 19,
                        '拒收退回仓库费（续重）' => 1,
                        '拒收退回仓库费（续重费用）' => 3,
                        '仓储费（免费30天）' => 0,
                        '仓储费（0.5元/天*票）' => 0.5,
                        '转寄打单费' => 5,
                        '当地转' => [
                            'BKK' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                            ],
                            '其它城市' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                            ],
                            '特别偏远' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                                '加收费用' => 40,
                            ],
                        ],
                    ],
                    '化妆品类' => [
                        '首重' => 1,
                        '首重费用' => 37,
                        '续重' => 1,
                        '续重费用' => 10,
                        'COD手续费' => 15,
                        'COD手续费比例（超过10000TH）' => 0.015,
                        '周六、早上派送（票）' => 20,
                        '拒收退回仓库费（首重）' => 2,
                        '拒收退回仓库费（首重费用）' => 19,
                        '拒收退回仓库费（续重）' => 1,
                        '拒收退回仓库费（续重费用）' => 3,
                        '仓储费（免费30天）' => 0,
                        '仓储费（0.5元/天*票）' => 0.5,
                        '转寄打单费' => 5,
                        '当地转' => [
                            'BKK' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                            ],
                            '其它城市' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                            ],
                            '特别偏远' => [
                                '当地转派送费首重' => 2,
                                '当地转派送费首重费用' => 19,
                                '当地转派送费续重' => 1,
                                '当地转派送费续重费用' => 3,
                                '加收费用' => 40,
                            ],
                        ],
                    ],
                ],
            ],
            '森鸿' => [
                'TW' => [
                    '首重' => 1,
                    '首重费用(台币)' => 140,
                    '续重' => 1,
                    '续重费用(台币)' => 50,
                    '代收款手续费（<5000台币）' => 0,
                    '代收款手续费（>5000台币）(台币)' => 30,
                    '改派地址或重发(台币)' => 70,
                    '改派地址或重发手续费(台币)' => 30,
                    '离岛区域加收(台币)' => 230,
                    '转寄或重出仓储理货费(台币)' => 20,
                    '转寄或重出黑猫派送费(台币)' => 70,
                    '转寄或重出代收手续费(台币)' => 30,
                    '货物尺寸限制(cm)' => 90,
                    '货物尺寸限制(kg)' => 10,
                ],
                'HK' => [
                    '首重' => 1,
                    '首重费用' => 40,
                    '续重' => 1,
                    '续重费用' => 3,
                    '体积重计算' => 6000,
                    '体积重计算方式' => '谁重用谁', //实重   体积重    谁重用谁   平均
                    '仓储理货费' => 15,
                    '首重(重出)' => 1,
                    '首重费用(重出)' => 40,
                    '续重(重出)' => 1,
                    '续重费用(重出)' => 3,
                ],
            ],
            'K1' => [
                //西马
                'MY0' => [
                    'P' => [
                        '首重' => 1,
                        '首重费用' => 30,
                        '续重' => 1,
                        '续重费用' => 18,
                        '拒签退回费用' => 0,
                        'COD手续费最低值' => 15,
                        'COD手续费比例' => 0.03,
                        '换单费' => 5,
                        '换单首重' => 1,
                        '换单首重费用' => 15,
                        '换单续重' => 1,
                        '换单续重费用' => 1.5,
                        '仓租费免费天数' => 15,
                        '仓租费(元/kg&天)' => 0.5,
                        '参考时效' => '3-5',
                    ],
                    'M' => [
                        '首重' => 1,
                        '首重费用' => 35,
                        '续重' => 1,
                        '续重费用' => 20,
                        '拒签退回费用' => 0,
                        'COD手续费最低值' => 15,
                        'COD手续费比例' => 0.03,
                        '换单费' => 5,
                        '换单首重' => 1,
                        '换单首重费用' => 15,
                        '换单续重' => 1,
                        '换单续重费用' => 1.5,
                        '仓租费免费天数' => 15,
                        '仓租费(元/kg&天)' => 0.5,
                        '参考时效' => '3-5',
                    ],
                ],
                'MY1' => [
                    'P' => [
                        '首重' => 1,
                        '首重费用' => 45,
                        '续重' => 1,
                        '续重费用' => 40,
                        '拒签退回费用' => 0,
                        'COD手续费最低值' => 15,
                        'COD手续费比例' => 0.03,
                        '换单费' => 5,
                        '换单首重' => 1,
                        '换单首重费用' => 30,
                        '换单续重' => 1,
                        '换单续重费用' => 30,
                        '仓租费免费天数' => 15,
                        '仓租费(元/kg&天)' => 0.5,
                        '参考时效' => '4-7',
                    ],
                    'M' => [
                        '首重' => 1,
                        '首重费用' => 50,
                        '续重' => 1,
                        '续重费用' => 45,
                        '拒签退回费用' => 0,
                        'COD手续费最低值' => 15,
                        'COD手续费比例' => 0.03,
                        '换单费' => 5,
                        '换单首重' => 1,
                        '换单首重费用' => 30,
                        '换单续重' => 1,
                        '换单续重费用' => 30,
                        '仓租费免费天数' => 15,
                        '仓租费(元/kg&天)' => 0.5,
                        '参考时效' => '4-7',
                    ],
                ],
            ],
            'imile' => [
                'UAE' => [
                    '迪拜' => [
                        'P' => [
                            '头程费用/kg' => 29,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 18,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                        'M' => [
                            '头程费用/kg' => 32,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 18,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                        '特殊货' => [
                            '头程费用/kg' => 37,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 18,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                    ],
                    '沙迦' => [
                        'P' => [
                            '头程费用/kg' => 29,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 18,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                        'M' => [
                            '头程费用/kg' => 32,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 18,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                        '特殊货' => [
                            '头程费用/kg' => 37,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 18,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                    ],
                    '阿联酋其他地区' => [
                        'P' => [
                            '头程费用/kg' => 29,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 25,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                        'M' => [
                            '头程费用/kg' => 32,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 25,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                        '特殊货' => [
                            '头程费用/kg' => 37,
                            '头程最低重量(kg)' => 5,
                            '时间' => '3-5',
                            '派送首重' => 5,
                            '派送首重费用(AED)' => 25,
                            '派送续重' => 1,
                            '派送续重费用(AED)' => 3,
                            '代收货款服务费比例' => 0.03,
                        ],
                    ],
                ],
            ],
            '合联' => [
                'UAE' => [
                    '运费' => [    //重量按百g 计算 向上取整  在哪两个 k值之间
                        1 => 27,
                        2 => 30,
                        3 => 32,
                        4 => 35,
                        5 => 38,
                        6 => 41,
                        7 => 44,
                        8 => 47,
                        9 => 50,
                        10 => 53,
                        11 => 56,
                        12 => 59,
                        13 => 62,
                        14 => 65,
                        15 => 68,
                        16 => 71,
                        17 => 74,
                        18 => 76,
                        19 => 79,
                        20 => 82,
                        21 => 85,
                        22 => 88,
                        23 => 91,
                        24 => 94,
                        25 => 97,
                        26 => 100,
                        27 => 103,
                        28 => 106,
                        29 => 109,
                        30 => 112,
                        31 => 115,
                        32 => 118,
                        33 => 120,
                        34 => 123,
                        35 => 126,
                        36 => 129,
                        37 => 132,
                        38 => 135,
                        39 => 138,
                        40 => 141,
                    ],
                    'COD手续费($)' => [
                        1 => 2.8,
                        138 => 4,
                        273 => 6.4,
                        410 => 7.8,
                        1366 => 10.7,
                    ],
                ],
            ],
            '和洋' => [
                'PHL' => [
                    '首重' => 0.5,
                    '首重费用' => 40,
                    '续重' => 0.5,
                    '续重费用' => 20,
                    '体积重计算' => 6000,
                    '体积重计算方式' => '谁重用谁', //实重   体积重    谁重用谁   平均
                    '代收手续费比例' => 0.03,
                    '参考时效(马尼拉省)' => '1-2',
                    '参考时效(马尼拉省外省1)' => '2-3',
                    '参考时效(马尼拉省外省2)' => '3-5',
                    '改派或重发费用' => 20,
                    '退款手续费比例' => 0.05,
                    '渠道代码' => 'PHCOD',
                ],
            ],
            '翊达' => [
                'ID' => [],
            ],
            '云路' => [],
            'CDS马来' => [],
            'CDS印尼（鸿腾全运）' => [],
            'TJM' => [],
            '汉邮' => [],
            '皇家' => [],
            '东丰' => [],
        ];
    }
}
