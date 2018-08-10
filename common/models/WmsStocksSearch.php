<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsStocks;

/**
 * WmsStocksSearch represents the model behind the search form about `common\models\WmsStocks`.
 */
class WmsStocksSearch extends WmsStocks
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'goods_id', 'qty', 'status', 'new_order_id'], 'integer'],
            [['create_date', 'expired_day', 'country', 'sales', 'track_company', 'track_number', 'destroy_time', 'reservoir_area', 'location', 'print_time', 'outbound_time','sku'], 'safe'],
            [['fee'], 'number'],
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
        $query = WmsStocks::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }
        $controller = Yii::$app->controller->action->actionMethod;

        if($controller == 'actionTest'){
            $query->select(['status'])->orderBy('status asc');
            //$dataProvider->pagination->defaultPageSize = $params['pagination'];
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'goods_id' => $this->goods_id,
            'qty' => $this->qty,
            'create_date' => $this->create_date,
            'expired_day' => $this->expired_day,
            'status' => $this->status,
            'fee' => $this->fee,
            'new_order_id' => $this->new_order_id,
            'destroy_time' => $this->destroy_time,
            'print_time' => $this->print_time,
            'outbound_time' => $this->outbound_time,
        ]);


        $query->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'sales', $this->sales])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'track_company', $this->track_company])
            ->andFilterWhere(['like', 'track_number', $this->track_number])
            ->andFilterWhere(['like', 'reservoir_area', $this->reservoir_area])
            ->andFilterWhere(['like', 'location', $this->location]); 
        return $dataProvider;
    }
}
