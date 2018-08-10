<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsIcBillDetail;

/**
 * WmsIcBillDetailSearch represents the model behind the search form of `common\models\WmsIcBillDetail`.
 */
class WmsIcBillDetailSearch extends WmsIcBillDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'ic_bill_id', 'difcount'], 'integer'],
            [['create_time', 'sku', 'spu'], 'safe'],
            [['goods_count'], 'number'],
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
        $query = WmsIcBillDetail::find();

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
            'goods_id' => $this->goods_id,
            'goods_count' => $this->goods_count,
            'ic_bill_id' => $this->ic_bill_id,
            'difcount' => $this->difcount,
        ]);

        $query->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'spu', $this->spu]);

        return $dataProvider;
    }
}
