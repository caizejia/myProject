<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsProblems;
use common\models\WmsProblemsSearch;
use api\modules\v1\wms\controllers\CommonController; 
use yii\helpers\ArrayHelper;

/**
 * ProblemsController implements the CRUD actions for WmsProblems model.
 */
class ProblemsController extends CommonController
{
    public $modelClass = 'common\models\WmsProblems';

    public  function actions()
    {
        $actions = parent::actions(); 
         
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    {
        $searchModel =  new WmsProblemsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams); 
        // $posts =  ArrayHelper::toArray( $dataProvider->getModels() );
        return $dataProvider;
    }
 
}
