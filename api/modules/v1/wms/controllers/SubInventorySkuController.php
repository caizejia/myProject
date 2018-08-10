<?php

namespace api\modules\v1\wms\controllers;
 
use yii\data\ActiveDataProvider; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsSubInventorySku; 
use common\models\WmsSubInventorySkuSearch; 

//这里可以作为restfull 普通业务的例子参考
class SubInventorySkuController extends CommonController
{
    public $modelClass = 'common\models\WmsSubInventorySku';
    
   
    public  function actions()
    {
        $actions = parent::actions(); 
        //unset($actions['delete']); 
        //unset($actions['update']);
         
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    { 
        $searchModel =  new WmsSubInventorySkuSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams); 
        return $dataProvider;
    }

    //pda扫描，货物从库位进出，记录日志，改变库位的货物数量
    public function actionScan()
    {
        $searchModel =  new WmsSubInventorySku();
        $param = \Yii::$app->request->post();
        if(isset($param['sku']) AND isset($param['sub_inventory_id']) AND isset($param['num']) AND isset($param['type'])){
            $status = $searchModel->in_or_out($param['sku'] , $param['sub_inventory_id'] , $param['num'] , $param['type']); 
        }else{
            return ['status'=>0,'msg'=>'上传数据不完整'];
        }
        
        if($status){
            return ['status'=>1,'msg'=>'ok'];
        }
        return ['status'=>0,'msg'=>'扫描出错'];
    }
    
      
    
}
