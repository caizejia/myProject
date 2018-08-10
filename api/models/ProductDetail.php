<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_product_detail".
 *
 * @property string $id 产品详情自增id
 * @property string $pid 产品id
 * @property string $description 产品描述
 * @property string $host 站名
 * @property string $domain 域名
 * @property string $additional 产品参数
 * @property string $template 关联模板
 * @property string $related_id 推荐产品关联id
 * @property string $buy_link 产品购买链接
 * @property string $ad_web_list 投放网站id集合（json格式）
 * @property int $suitable_sex 适用性别（0所有 1男 2女）
 * @property string $think 选品思路
 * @property int $is_del 是否删除（0否 1是）
 * @property string $jd_link 京东链接
 * @property string $cloak_link 隐秘url
 * @property string $next_price 第二件价格
 * @property int $buy_one_get_two 赠送数量
 * @property int $end_hour 产品结束时间（单位：小时）
 * @property string $actual_price 实际价格
 * @property string $sales_info 促销信息
 * @property string $designer_id 设计师id
 * @property string $declare_cname 中文申报名
 * @property string $declare_ename 申报英文名
 * @property string $declare_code 申报编码
 * @property string $declare_price 申报价格
 * @property string $remark 产品备注
 * @property int $min_num 采购最少购买数量
 * @property string $gift 赠品
 * @property string $gift_qty
 * @property string $product_type 货物类型 P普货 M敏感货
 * @property int $is_cloak 是否隐秘
 */
class ProductDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_product_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'designer_id', 'min_num', 'gift_qty'], 'integer'],
            [['description', 'host', 'domain', 'additional', 'related_id', 'buy_link', 'ad_web_list', 'think', 'declare_code', 'gift_qty'], 'required'],
            [['description', 'additional', 'think'], 'string'],
            [['next_price', 'actual_price', 'declare_price'], 'number'],
            [['host'], 'string', 'max' => 50],
            [['domain', 'sales_info', 'declare_cname', 'declare_ename', 'declare_code', 'remark', 'gift'], 'string', 'max' => 255],
            [['template', 'related_id', 'ad_web_list', 'cloak_link'], 'string', 'max' => 128],
            [['buy_link', 'jd_link'], 'string', 'max' => 500],
            [['suitable_sex', 'is_del', 'product_type', 'is_cloak'], 'string', 'max' => 1],
            [['buy_one_get_two', 'end_hour'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'description' => 'Description',
            'host' => 'Host',
            'domain' => 'Domain',
            'additional' => 'Additional',
            'template' => 'Template',
            'related_id' => 'Related ID',
            'buy_link' => 'Buy Link',
            'ad_web_list' => 'Ad Web List',
            'suitable_sex' => 'Suitable Sex',
            'think' => 'Think',
            'is_del' => 'Is Del',
            'jd_link' => 'Jd Link',
            'cloak_link' => 'Cloak Link',
            'next_price' => 'Next Price',
            'buy_one_get_two' => 'Buy One Get Two',
            'end_hour' => 'End Hour',
            'actual_price' => 'Actual Price',
            'sales_info' => 'Sales Info',
            'designer_id' => 'Designer ID',
            'declare_cname' => 'Declare Cname',
            'declare_ename' => 'Declare Ename',
            'declare_code' => 'Declare Code',
            'declare_price' => 'Declare Price',
            'remark' => 'Remark',
            'min_num' => 'Min Num',
            'gift' => 'Gift',
            'gift_qty' => 'Gift Qty',
            'product_type' => 'Product Type',
            'is_cloak' => 'Is Cloak',
        ];
    }
}
