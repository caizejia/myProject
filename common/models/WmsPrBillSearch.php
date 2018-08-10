<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsPrBill;

/**
 * WmsPrBillSearch represents the model behind the search form about `common\models\WmsPrBill`.
 */
class WmsPrBillSearch extends WmsPrBill
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'status', 'action_user_id', 'supplier_id', 'warehouse_id', 'purchases_detail_id', 'pw_bill_detail_id'], 'integer'],
            [['rejection_goods_count', 'rejection_goods_price', 'rejection_money'], 'number'],
            [['action_time', 'create_time'], 'safe'],
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
        $query = WmsPrBill::find();

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
            'goods_id' => $this->goods_id,
            'rejection_goods_count' => $this->rejection_goods_count,
            'rejection_goods_price' => $this->rejection_goods_price,
            'rejection_money' => $this->rejection_money,
            'status' => $this->status,
            'action_time' => $this->action_time,
            'action_user_id' => $this->action_user_id,
            'supplier_id' => $this->supplier_id,
            'create_time' => $this->create_time,
            'warehouse_id' => $this->warehouse_id,
            'purchases_detail_id' => $this->purchases_detail_id,
            'pw_bill_detail_id' => $this->pw_bill_detail_id,
        ]);

        $query->asArray();

        return $dataProvider;
    }
}
