<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsInventory;

/**
 * WmsInventorySearch represents the model behind the search form of `common\models\WmsInventory`.
 */
class WmsInventorySearch extends WmsInventory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'balance_count', 'afloat_count', 'lock_count', 'warehouse_id', 'hz_id'], 'integer'],
            [['goods_id'], 'safe'],
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

        $query = WmsInventory::find();

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
            'balance_count' => $this->balance_count,
            'afloat_count' => $this->afloat_count,
            'lock_count' => $this->lock_count,
            'warehouse_id' => $this->warehouse_id,
            'hz_id' => $this->hz_id,
        ]);

        $query->andFilterWhere(['=', 'goods_id', $this->goods_id]);

        return $dataProvider;
    }
}
