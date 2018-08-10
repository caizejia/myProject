<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsPurchasesDetail;

/**
 * PurchasesDetailSearch represents the model behind the search form about `api\models\PurchasesDetail`.
 */
class WmsPurchasesDetailSearch extends WmsPurchasesDetail
{
    public $create_time_start;
    public $create_time_end;
    public $sku_code;
    public $ref;
    public $supplier_name;
    public $supplier_platform;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'good_id', 'count', 'purchase_id', 'add_library_count', 'minus_library_count', 'status', 'action_user_id', 'confirm_user_id'], 'integer'],
            [['total_price', 'price'], 'number'],
            [['desc', 'memo', 'action_time', 'finish_time', 'confirm_time', 'pw_bill_id', 'ref', 'supplier_ref', 'supplier_name', 'supplier_platform', 'sku_code', 'create_time_start', 'create_time_end'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = WmsPurchasesDetail::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'totalCount' => $query->count()
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //因数据库supplier还未确定暂时先用目前的自定义数据
        $query->joinWith([
            'purchases'=>function($query){
                $query->joinWith([
                    'supplier'/*=>function($query){
                        $query->joinWith(['productSku']);
                    }*/
                ]);
            },
            'productDetails'
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'good_id' => $this->good_id,
            'count' => $this->count,
            'total_price' => $this->total_price,
            'price' => $this->price,
            'wms_purchases_detail.purchase_id' => $this->purchase_id,
            'add_library_count' => $this->add_library_count,
            'minus_library_count' => $this->minus_library_count,
            'wms_purchases_detail.status' => $this->status,
            'action_user_id' => $this->action_user_id,
            'action_time' => $this->action_time,
            'finish_time' => $this->finish_time,
            'confirm_user_id' => $this->confirm_user_id,
            'confirm_time' => $this->confirm_time,
        ]);

        $query->andFilterWhere(['like', 'wms_purchases.ref', $this->ref])  //系统采购单号
            ->andFilterWhere(['between', 'wms_purchases.create_time', $this->create_time_start, $this->create_time_end])  //采购订单生成时间
            ->andFilterWhere(['like', 'supplier_ref', $this->supplier_ref])  //平台单号
            ->andFilterWhere(['like', 'wms_purchases.supplier_name', $this->supplier_name])  //供应商名称
            ->andFilterWhere(['like', 'wms_purchases.supplier_platform', $this->supplier_platform])  //平台
            ->andFilterWhere(['like', 'sku_code', $this->sku_code])  //sku
            ->andFilterWhere(['like', 'desc', $this->desc])  //描述
            ->andFilterWhere(['like', 'memo', $this->memo]);  //备注

        $query->asArray();

        return $dataProvider;
    }

}
