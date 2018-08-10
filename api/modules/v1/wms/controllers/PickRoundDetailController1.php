<?php

namespace api\modules\v1\wms\controllers;
 
use yii\data\ActiveDataProvider; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsPickRoundDetail; 

//这里可以作为restfull 普通业务的例子参考
class PickRoundDetailController extends CommonController
{
    public $modelClass = 'common\models\WmsPickRoundDetail';

    public  function actions()
    {
        $actions = parent::actions();
        // 禁用"delete" 和 "create" 动作
//        unset($actions['delete']);
//        unset( $actions['create']);
//        unset($actions['update']);
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    {
    	$detail = [];
        //$detail =  new WmsPickRoundDetail();
        /*if(isset($_GET['pick_round_id'])){ 
        	$detail = WmsPickRoundDetail::find()
		    ->where(['pick_round_id' => $_GET['pick_round_id']])
		    ->orderBy('id')
		    ->all(); 
		    $detail = $this->wrapData($detail );
        }*/

        $query = WmsPickRoundDetail::find()->where(['pick_round_id' => $_GET['pick_round_id']]); 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'totalCount' => $query->count()
            ],
        ]);  
      
 
        return $dataProvider;
    }
}
