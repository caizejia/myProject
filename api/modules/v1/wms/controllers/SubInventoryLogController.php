<?php
namespace api\modules\v1\wms\controllers;

use yii\data\ActiveDataProvider;
use common\models\Adminuser;
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsSubInventorylog;
use common\models\WmsSubInventoryLogSearch;

//这里可以作为restfull 普通业务的例子参考
class SubInventoryLogController extends CommonController
{
    public $modelClass = 'common\models\WmsSubInventorylog';
    
   
    /*public  function actions()
    {
        $actions = parent::actions(); 
        unset($actions['index']);// 以下重写了原来的 index
        unset($actions['create']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    { 
        $searchModel =  new WmsSubInventoryLogSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams); 
        return $dataProvider;
    }*/
//    public function actionCreate()
//    {
//        $model = new WmsTransfer();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        }
//
//        return $this->render('create', [
//            'model' => $model,
//        ]);
//    }
    
}