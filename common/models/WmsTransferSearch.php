<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsTransfer;

/**
 * WmsTransferSearch represents the model behind the search form of `common\models\WmsTransfer`.
 */
class WmsTransferSearch extends WmsTransfer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'action_user_id', 'hzfrom_id', 'hzto_id', 'status', 'outstatus', 'instatus'], 'integer'],
            [['ref', 'action_time', 'wfrom_id', 'wto_id', 'remark'], 'safe'],
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
        $query = WmsTransfer::find();

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
            'action_time' => $this->action_time,
            'action_user_id' => $this->action_user_id,
            'hzfrom_id' => $this->hzfrom_id,
            'hzto_id' => $this->hzto_id,
            'status' => $this->status,
            'outstatus' => $this->outstatus,
            'instatus' => $this->instatus,
        ]);

        $query->andFilterWhere(['like', 'ref', $this->ref])
            ->andFilterWhere(['like', 'wfrom_id', $this->wfrom_id])
            ->andFilterWhere(['like', 'wto_id', $this->wto_id])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
