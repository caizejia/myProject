<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsIcBill;

/**
 * WmsIcBillSearch represents the model behind the search form of `common\models\WmsIcBill`.
 */
class WmsIcBillSearch extends WmsIcBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'warehouse_id', 'bill_status', 'bill_type'], 'integer'],
            [['ref', 'create_time', 'input_user_id', 'memo', 'bill_date'], 'safe'],
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
        $query = WmsIcBill::find();

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
            'create_time' => $this->create_time,
            'warehouse_id' => $this->warehouse_id,
            'bill_status' => $this->bill_status,
            'bill_type' => $this->bill_type,
            'bill_date' => $this->bill_date,
        ]);

        $query->andFilterWhere(['like', 'ref', $this->ref])
            ->andFilterWhere(['like', 'input_user_id', $this->input_user_id])
            ->andFilterWhere(['like', 'memo', $this->memo]);

        return $dataProvider;
    }
}
