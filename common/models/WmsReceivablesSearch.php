<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsReceivables;

/**
 * WmsReceivablesSearch represents the model behind the search form about `common\models\WmsReceivables`.
 */
class WmsReceivablesSearch extends WmsReceivables
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ref', 'action_time', 'create_time'], 'safe'],
            [['rv_money', 'act_money', 'balance_money'], 'number'],
            [['purchases_detail_id', 'action_user_id', 'status'], 'integer'],
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
        $query = WmsReceivables::find();

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
            'rv_money' => $this->rv_money,
            'act_money' => $this->act_money,
            'balance_money' => $this->balance_money,
            'pr_bill_id' => $this->pr_bill_id,
            'purchases_detail_id' => $this->purchases_detail_id,
            'action_time' => $this->action_time,
            'create_time' => $this->create_time,
            'action_user_id' => $this->action_user_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'ref', $this->ref]);

        return $dataProvider;
    }
}
