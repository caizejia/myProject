<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsOtherDetail;

/**
 * WmsOtherDetailSearch represents the model behind the search form of `common\models\WmsOtherDetail`.
 */
class WmsOtherDetailSearch extends WmsOtherDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'outqty', 'ws_bill_id'], 'integer'],
            [['sku', 'memo'], 'safe'],
            [['goods_money', 'goods_price'], 'number'],
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
        $query = WmsOtherDetail::find();

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
            'outqty' => $this->outqty,
            'goods_money' => $this->goods_money,
            'goods_price' => $this->goods_price,
            'ws_bill_id' => $this->ws_bill_id,
        ]);

        $query->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'memo', $this->memo]);

        return $dataProvider;
    }
}
