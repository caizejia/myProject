<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WmsInventory1;

/**
 * ArticleSearch represents the model behind the search form about `common\models\Article`.
 */
class WmsInventory1Search extends WmsInventory1
{
    /*/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id',  'goods_id', 'hz_id'], 'integer']
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
        $query = WmsInventory1::find();
        //连接 join
        $query->joinWith(['good']);
          
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
            'wms_inventory.id' => $this->id,
            'wms_inventory.goods_id' => $this->goods_id,
            
        ]);

        //搜索功能
        if(isset($params['warehouse_id'])){
            $query->andWhere(['like', 'warehouse_id', $params['warehouse_id']]);
        }
        if(isset($params['sku'])){
            $query->andWhere(['like', 'sku_code', $params['sku']]);
        }

        //打印调试语句
        //print_r($query->createCommand()->getRawSql());die;
        return $dataProvider;
    }
}
