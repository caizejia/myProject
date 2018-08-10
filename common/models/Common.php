<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/13
 * Time: 14:56
 */

namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;

class Common extends \yii\db\ActiveRecord
{
    public function sortZdy($dataProvider,$params,$responseFormat = \yii\web\Response::FORMAT_JSON){
        /**
         * HTML: implemented by yii\web\HtmlResponseFormatter.
         * XML: implemented by yii\web\XmlResponseFormatter.
         * JSON: implemented by yii\web\JsonResponseFormatter.
         * JSONP: implemented by yii\web\JsonResponseFormatter.
         * RAW: use this format if you want to send the response directly without applying any formatting.
         */
        \Yii::$app->response->format = $responseFormat;

        $datas = $dataProvider->getModels();
        $datas = ArrayHelper::toArray($datas);

        $sort = trim($params['sort']);
        if(substr( $sort, 0, 1 ) == '-'){
            $sort_field = substr($sort,strpos($sort,'-')+1);
            $sort_order = 'desc';
        }else{
            if($sort){
                $sort_field = $sort;
                $sort_order = 'asc';
            }
        }
        if($sort_field){
            $key_arrays = [];
            foreach($datas as $val){
                $key_arrays[] = $val[$sort_field];
            }
            /**
             * SORT_REGULAR - 将项目按照通常方法比较（不修改类型）
             * SORT_NUMERIC - 按照数字大小比较
             * SORT_STRING - 按照字符串比较
             * SORT_LOCALE_STRING - 根据当前的本地化设置，按照字符串比较。 它会使用 locale 信息，可以通过 setlocale() 修改此信息。
             * SORT_NATURAL - 以字符串的"自然排序"，类似 natsort()
             * SORT_FLAG_CASE - 可以组合 (按位或 OR) SORT_STRING 或者 SORT_NATURAL 大小写不敏感的方式排序字符串。
             */
            if($sort_order == 'desc'){
                array_multisort($key_arrays,SORT_DESC,SORT_REGULAR,$datas);
            }else{
                array_multisort($key_arrays,SORT_ASC,SORT_REGULAR,$datas);
            }
        }
        return $datas;
    }
}