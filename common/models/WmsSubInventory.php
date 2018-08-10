<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_sub_inventory".  库区库位
 *
 * @property integer $id
 * @property integer $inventory_id
 * @property string $location
 * @property string $code
 * @property string $memo
 */
class WmsSubInventory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_sub_inventory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id'], 'integer'],
            [['location'], 'string', 'max' => 50],
            [['code', 'memo'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => '仓库的id',
            'location' => '区位，如A区，B区等，方便拣货批次',
            'code' => '库区名称 如 A-01-02',
            'memo' => '备注',
        ];
    }


}
