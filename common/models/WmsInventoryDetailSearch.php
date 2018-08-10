<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsInventoryDetail;

/**
 * ArticleSearch represents the model behind the search form about `common\models\Article`.
 */
class WmsInventoryDetailSearch extends WmsInventoryDetail
{
    /*/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id',  'goods_id'], 'integer'] 
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
     * 作为例子参考
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = WmsInventoryDetail::find(); 
       
        // add conditions that should always apply here 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            //如果匹配不同，想不返回，请去掉注释
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1'); 
            return $dataProvider;
        }
        $controller = Yii::$app->controller->action->actionMethod;
        // grid filtering conditions
        //匹配过滤
        $query->andFilterWhere([
            'id' => $this->id,
            'ref_type' => $this->ref_type,
            
        ]);    
         //搜索功能
        if(isset($params['color'])){
            $query->andWhere(['like', 'color', $params['color']]);
        }
        if(isset($params['goods_id'])){
            $query->andWhere(['=', 'goods_id', $params['goods_id']]);
        }
 
        //打印调试语句
        //print_r($query->createCommand()->getRawSql());die; 
        return $dataProvider;
    }
}
