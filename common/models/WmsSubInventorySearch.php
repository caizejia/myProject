<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsSubInventory;

/**
 * ArticleSearch represents the model behind the search form about `common\models\Article`.
 */
class WmsSubInventorySearch extends WmsSubInventory
{
    /*/**
     * @inheritdoc
     */
    /*public function rules()
    {
        return [
            [['id',  'goods_id'], 'integer'] 
        ];
    }*/

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    

    /**
     * 作为例子参考
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = WmsSubInventory::find(); 
           
        // add conditions that should always apply here 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            //如果匹配不同，想不返回，请去掉注释
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1'); 
            return $dataProvider;
        }

        // grid filtering conditions
        //匹配过滤
        $query->andFilterWhere([
            'id' => $this->id
        ]);    

        //搜索功能
        if(isset($params['warehouse_id'])){
            $query->andFilterWhere(['=', 'warehouse_id', $params['warehouse_id']]);
        }
        if(isset($params['location'])){
            $query->andFilterWhere(['=', 'location', $params['location']]);
        }
        if(isset($params['code'])){
            $query->andFilterWhere(['like', 'code', $params['code']]);
        }
        //打印调试语句
        //print_r($query->createCommand()->getRawSql());die; 
        return $dataProvider;
    }
}
