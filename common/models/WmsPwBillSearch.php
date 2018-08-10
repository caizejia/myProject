<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsPwBill;

/**
 * PwBillSearch represents the model behind the search form about `api\models\PwBill`.
 */
class WmsPwBillSearch extends WmsPwBill
{
    public $create_time_start;
    public $create_time_end;
    public $supplier_ref;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'goods_count', 'status', 'action_user_id', 'supplier_id', 'warehouse_id', 'purchases_detail_id'], 'integer'],
            [['ref', 'action_time', 'create_time', 'memo', 'create_time_start', 'create_time_end', 'supplier_ref'], 'safe'],
            [['goods_money', 'goods_price'], 'number'],
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
        $query = WmsPwBill::find();

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

        $query->joinWith([
            'productDetails',
            'purchasesDetail' => function($query){
                $query->joinWith(['purchases']);
            },
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'goods_id' => $this->goods_id,
            'goods_count' => $this->goods_count,
            'goods_money' => $this->goods_money,
            'goods_price' => $this->goods_price,
            'wms_pw_bill.status' => $this->status,
            'action_time' => $this->action_time,
            'action_user_id' => $this->action_user_id,
            'create_time' => $this->create_time,
            'supplier_id' => $this->supplier_id,
            'warehouse_id' => $this->warehouse_id,
            'purchases_detail_id' => $this->purchases_detail_id,
        ]);

        $query->andFilterWhere(['=', 'ref', $this->ref])
            ->andFilterWhere(['=', 'supplier_ref', $this->supplier_ref])
            ->andFilterWhere(['like', 'memo', $this->memo]);

        $query->asArray();

        return $dataProvider;
    }
}
