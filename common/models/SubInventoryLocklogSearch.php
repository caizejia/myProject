<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SubInventoryLocklog;

/**
 * SubInventoryLocklogSearch represents the model behind the search form of `common\models\SubInventoryLocklog`.
 */
class SubInventoryLocklogSearch extends SubInventoryLocklog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sub_inventory_id', 'goods_id', 'number', 'lock_count', 'type', 'userid'], 'integer'],
            [['sku', 'comment', 'create_time', 'ref'], 'safe'],
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
        $query = SubInventoryLocklog::find();

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
            'sub_inventory_id' => $this->sub_inventory_id,
            'goods_id' => $this->goods_id,
            'number' => $this->number,
            'lock_count' => $this->lock_count,
            'type' => $this->type,
            'create_time' => $this->create_time,
            'userid' => $this->userid,
        ]);

        $query->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'ref', $this->ref]);

        return $dataProvider;
    }
}
