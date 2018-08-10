<?php

namespace common\models;

use Yii;
use api\models\OrderGoods;

/**
 * This is the model class for table "wms_purchases_detail".
 *
 * @property integer $id
 * @property integer $good_id
 * @property string $supplier_ref
 * @property string $link
 * @property integer $should_count
 * @property integer $count
 * @property string $price
 * @property string $total_price
 * @property string $logistics
 * @property integer $purchase_id
 * @property integer $add_library_count
 * @property integer $minus_library_count
 * @property integer $status
 * @property string $desc
 * @property string $memo
 * @property integer $action_user_id
 * @property string $action_time
 * @property string $finish_time
 * @property integer $confirm_user_id
 * @property string $confirm_time
 * @property string $pw_bill_id
 */
class WmsPurchasesDetail extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public static $status = [
        0 => '待采购',
        1 => '已采购',
        2 => '缺货',
        3 => '取消单',
        4 => '待付款',
        5 => '已付款',
        6 => '付款失败',
        7 => '收货中',
        8 => '退款中',
        9 => '退货中',
        10 => '已完成',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_purchases_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['good_id', 'should_count', 'purchase_id', 'add_library_count', 'minus_library_count', 'status', 'desc'], 'required'],
            [['good_id', 'should_count', 'count', 'purchase_id', 'add_library_count', 'minus_library_count', 'status', 'action_user_id', 'confirm_user_id'], 'integer'],
            [['price', 'total_price'], 'number'],
            [['action_time', 'finish_time', 'confirm_time'], 'safe'],
            [['supplier_ref', 'link', 'memo', 'pw_bill_id'], 'string', 'max' => 255],
            [['logistics', 'desc'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'good_id' => 'SKU表ID（sku表wms_product_details',
            'supplier_ref' => '平台单号',
            'link' => '采购链接',
            'should_count' => '应采购的数量',
            'count' => '实际采购数量',
            'price' => '当前产品单价',
            'total_price' => '当前产品总价',
            'logistics' => '物流单号',
            'purchase_id' => '采购订单ID',
            'add_library_count' => '已入库数量',
            'minus_library_count' => '已出库数量 退货',
            'status' => '采购状态 0：待采购 1：待付款 2：缺货 3：已采购 4：待发货  5：到货待确认 6：取货取消 7：待退款 8：已退款 9：退款失败     是否全部入库 0不是 1是',
            'desc' => '描述',
            'memo' => '备注',
            'action_user_id' => '操作人员id，0 系统',
            'action_time' => '采购时间',
            'finish_time' => '交货完成时间',
            'confirm_user_id' => '采购审核人',
            'confirm_time' => '采购审核时间',
            'pw_bill_id' => '采购订单与采购入库单(收货单）的对应',
        ];
    }

    /**
     * @return
     * 连表wms_product_details
     */
    public function getProductDetails()
    {
        return $this->hasOne(WmsProductDetails::className(),['id' => 'good_id']);
    }

    /**
     * @return
     * 连表wms_purchases
     */
    public function getPurchases()
    {
        return $this->hasOne(WmsPurchases::className(),['id' => 'purchase_id']);
    }

    /**
     * @return
     * 连表wms_purchases
     */
    public function getPwBill()
    {
        return $this->hasOne(WmsPwBill::className(),['purchases_detail_id' => 'id']);
    }

    /**
     * @param $purchases_id
     * @param $before_status
     * @param $after_status
     * @return int
     * @throws \yii\db\Exception
     * 采购详情单状态改变
     */
    public function setPurchasesDetailStatus($purchases_detail_id,$before,$after)
    {
        $before_status = is_numeric($before)?$before:array_search($before, WmsPurchasesDetail::$status);
        $after_status = is_numeric($after)?$after:array_search($after, WmsPurchasesDetail::$status);
        $result = Yii::$app->db->createCommand()->update('wms_purchases_detail', [
            'status' => $after_status,
        ], ['id' => $purchases_detail_id, 'status' => $before_status])->execute();

        return $result;
    }

    /**
     * @param $good_id
     * @return mixed
     * 获取采购产品单价的历史最低价
     */
    public function getLowestPrice($good_id)
    {
        $price = self::find()->where(['=', 'good_id', $good_id])->min('price');
        return $price;
    }

    /**
     * @param $sku
     * @param $day
     * @return float|int
     * 获取过去时间段内的销量
     */
    public function getDaySalesVolume($sku, $day)
    {
        $end_time = time();
        $start_time = $end_time -  86400000 * $day;
        $data = OrderGoods::find()->select('number')->where(['like', 'sku_code', $sku])->andWhere(['between', 'create_time', $start_time, $end_time])->column();
        $num = array_sum($data);
        return $num;
    }
}
