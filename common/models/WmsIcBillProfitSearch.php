<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsIcBillProfit;

/**
 * WmsIcBillProfitSearch represents the model behind the search form of `common\models\WmsIcBillProfit`.
 */
class WmsIcBillProfitSearch extends WmsIcBillProfit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ic_bill_id', 'goods_id', 'number', 'actual_number', 'profit_number', 'loss_number', 'loss_cost', 'profit_user', 'loss_user'], 'integer'],
            [['spu', 'sku', 'create_time', 'status', 'product_name', 'comment', 'profit_number_time', 'loss_number_time'], 'safe'],
            [['profit_cost', 'total'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = WmsIcBillProfit::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ic_bill_id' => $this->ic_bill_id,
            'goods_id' => $this->goods_id,
            'create_time' => $this->create_time,
            'number' => $this->number,
            'actual_number' => $this->actual_number,
            'profit_number' => $this->profit_number,
            'profit_cost' => $this->profit_cost,
            'loss_number' => $this->loss_number,
            'loss_cost' => $this->loss_cost,
            'profit_user' => $this->profit_user,
            'loss_user' => $this->loss_user,
            'profit_number_time' => $this->profit_number_time,
            'loss_number_time' => $this->loss_number_time,
            'total' => $this->total,
        ]);

        $query->andFilterWhere(['like', 'spu', $this->spu])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'product_name', $this->product_name])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
