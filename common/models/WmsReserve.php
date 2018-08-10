<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_reserve".
 *
 * @property integer $id
 * @property string $sku
 * @property string $warehouse
 * @property string $location
 * @property integer $amount
 * @property string $create_time
 * @property string $update_time
 */
class WmsReserve extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_reserve';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sku', 'warehouse', 'location', 'amount', 'create_time'], 'required'],
            [['amount'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['sku'], 'string', 'max' => 25],
            [['warehouse', 'location'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'Sku',
            'warehouse' => 'Warehouse',
            'location' => 'Location',
            'amount' => 'Amount',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /*
     * 获取所有仓库 和 库位
     */
    public function actionList()
    {
        $reserve = Yii::$app->db->createCommand("select warehouse,location from wms_reserve group by warehouse,location")->queryAll();
        return json_encode([
            'data'  =>  $reserve,
            'msg'   =>  '获取数据成功',
            'code'  =>  200,
        ]);
    }

    /*
     * 查询某一仓库库位是否存在
     */
    public function actionQuery($warehouse = '本地仓')
    {
//        $this->enableCsrfValidation = false;
        if (Yii::$app->db->createCommand("select location from wms_reserve where warehouse = '{$warehouse}'")->queryOne()) {
            return json_encode([
                'data'  =>  NULL,
                'msg'   =>  '存在库位',
                'code'  =>  200,
            ]);
        } else {
            return json_encode([
                'data'  =>  NULL,
                'msg'   =>  '暂无库位!',
                'code'  =>  501
            ]);
        }
    }
}
