<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsSrBill;

/**
 * WmsSrBillSearch represents the model behind the search form of `common\models\WmsSrBill`.
 */
class WmsSrBillSearch extends WmsSrBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ref', 'action_time', 'action_user_id', 'customer_id', 'create_time'], 'safe'],
            [['status', 'warehouse_id', 'order_id'], 'integer'],
            [['inventory_money'], 'number'],
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
        $query = WmsSrBill::find();

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
            'status' => $this->status,
            'action_time' => $this->action_time,
            'create_time' => $this->create_time,
            'inventory_money' => $this->inventory_money,
            'warehouse_id' => $this->warehouse_id,
            'order_id' => $this->order_id,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'ref', $this->ref])
            ->andFilterWhere(['like', 'action_user_id', $this->action_user_id])
            ->andFilterWhere(['like', 'customer_id', $this->customer_id]);

        return $dataProvider;
    }
}
