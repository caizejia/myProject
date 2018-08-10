<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsPurchases;

/**
 * WmsPurchasesSearch represents the model behind the search form about `common\models\WmsPurchases`.
 */
class WmsPurchasesSearch extends WmsPurchases
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id'], 'integer'],
            [['ref', 'supplier_name', 'supplier_platform', 'create_time', 'status', 'memo'], 'safe'],
            [['money'], 'number'],
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
        $query = WmsPurchases::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'supplier_id' => $this->supplier_id,
            'create_time' => $this->create_time,
            'money' => $this->money,
        ]);

        $query->andFilterWhere(['=', 'ref', $this->ref])
            ->andFilterWhere(['like', 'supplier_name', $this->supplier_name])
            ->andFilterWhere(['like', 'supplier_platform', $this->supplier_platform])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['like', 'memo', $this->memo]);

        $query->asArray();

        return $dataProvider;
    }
}
