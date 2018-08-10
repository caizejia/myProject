<?php

namespace api\modules\v1\wms\controllers;
 
use yii\data\ActiveDataProvider; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsSubInventorySearch; 

//这里可以作为restfull 普通业务的例子参考
class SubInventoryController extends CommonController
{
    public $modelClass = 'common\models\WmsSubInventory';
    
   
    public  function actions()
    {
        $actions = parent::actions(); 
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    { 
        $searchModel =  new WmsSubInventorySearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams); 
        return $dataProvider;
    }
    



    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
