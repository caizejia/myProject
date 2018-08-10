<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsPayments;

/**
 * WmsPaymentsSearch represents the model behind the search form about `api\models\WmsPayments`.
 */
class WmsPaymentsSearch extends WmsPayments
{
    public $supplier_ref;
    public $supplier_name;
    public $supplier_platform;
    public $create_time_start;
    public $create_time_end;
    public $action_time_start;
    public $action_time_end;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'purchases_detail_id', 'pay_user_id', 'status', 'ca_type'], 'integer'],
            [['ref', 'create_time', 'action_time', 'supplier_ref', 'supplier_name', 'supplier_platform', 'create_time_start', 'create_time_end', 'action_time_start', 'action_time_end'], 'safe'],
            [['act_money', 'balance_money', 'pay_money'], 'number'],
            [['supplier_name', 'supplier_platform', 'pay_money'], 'string'],
            [['supplier_ref', 'status'],'trim'],
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
        $query = WmsPayments::find();

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

        $query->joinWith(['purchases','purchasesDetail']);
        // grid filtering conditions
        $query->andFilterWhere([
            'wms_payments.id' => $this->id,
            'act_money' => $this->act_money,
            'balance_money' => $this->balance_money,
            'pay_money' => $this->pay_money,
            'purchases_detail_id' => $this->purchases_detail_id,
            'pay_user_id' => $this->pay_user_id,
            'wms_payments.status' => $this->status,
            'ca_type' => $this->ca_type,
        ]);

        $query->andFilterWhere(['like', 'wms_payments.ref', $this->ref])  //付款单号
            ->andFilterWhere(['like', 'wms_purchases_detail.supplier_ref', $this->supplier_ref])  //平台单号
            ->andFilterWhere(['like', 'wms_purchases.supplier_platform', $this->supplier_platform])  //平台
            ->andFilterWhere(['like', 'wms_purchases.supplier_name', $this->supplier_name])  //供应商名称
            ->andFilterWhere(['between', 'wms_payments.create_time', $this->create_time_start, $this->create_time_end])  //付款单生成时间
            ->andFilterWhere(['between', 'wms_payments.action_time', $this->action_time_start, $this->action_time_end]);  //支付时间

        $query->asArray();

        return $dataProvider;
    }
}
