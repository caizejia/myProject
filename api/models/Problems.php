<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_problems".
 *
 * @property string $id
 * @property string $order_id 订单ID
 * @property int $problem 问题类型
 * @property int $status 问题状态
 * @property string $create_date 添加时间
 * @property string $description
 * @property string $track_number
 * @property string $new_price 新签收价
 */
class Problems extends \yii\db\ActiveRecord
{


    public $ids;
    public $actionType = [
        1 => '邮件',
        2 => '电话'
    ];
    //问题类型
    public $problemArray = [
        0 => '其他',
        1 => '地址有误',
        2 => '电话有误',
        3 => '电话无人接听',
        4 => '签收失败',
        5 => '无人收货',
        6 => '客户无法现金支付',
        7 => '客户要求取消订单',
        8 => '客户希望换一天送货',
        9 => '客户拒绝付款',
        10 => '电话地址都有误',
        11 => '客户说未曾订购',
        12 => '颜色不详',
        13 => '尺寸不详',
        14 => '件数不详'
    ];
    //问题状态
    public $statusArray = [
        0 => '处理中',
        4 => '重新派送',
        3 => '取消订单',
        1 => '拒签',
        2 => '签收'
    ];
    public $statusWorldToId = [
        '处理中'   => 0,
        '重新派送' => 4,
        '取消订单' => 3,
        '拒签'    => 1,
        '签收'    => 2
    ];
    public $importFile;
    public $emailTplName = [
        "address-error" => '地址不详细或不正确',
        "refused" => '客户拒收，询问客户是否有收到派送信息，以及是否仍然需要商品',
        "ref-buy" => '客户第一次拒收，再次下单，询问客户是否还需要',
        "phone-error" => '电话错误',
        "zcode" => '询问邮编',
        "qty" => '客户下了多个，询问客户需要几个',
    ];
    public $countyArray = [
        '香港' => '香港',
        '臺灣' => '臺灣',
        'TH' => '泰国',
        'MY' => '马来西亚',
        'SG' => '新加坡',
        'ID' =>'印尼',
        'UAE' =>'阿联酋',
    ];
    public $county;
    public $email;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_problems';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'track_number'], 'required'],
            [['order_id', 'problem', 'status'], 'integer'],
            [['create_time'], 'safe'],
            [['description'], 'string'],
            [['new_price'], 'number'],
            [['track_number'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'problem' => 'Problem',
            'status' => 'Status',
            'create_date' => 'Create Date',
            'description' => 'Description',
            'track_number' => 'Track Number',
            'new_price' => 'New Price',
        ];
    }
    /**
     * 文件上传
     * @return string
     */
    public function upload()
    {
        $file_name = time().'.'.$this->importFile->extension;
        $this->importFile->saveAs('uploads/' . $file_name);
        return $file_name;
    }
}
