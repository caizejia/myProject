<?php

namespace api\modules\v1\wms\controllers;
 
use yii\data\ActiveDataProvider;
use common\models\Article; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;

//作为restfull 例子参考
class ArticleController extends CommonController
{
    public $modelClass = 'common\models\Article';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data', //配置返回格式
    ];
   
    public  function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }
    
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        return new ActiveDataProvider(
                [
                    'query'=>$modelClass::find()->asArray(),
                    'pagination'=>['pageSize'=>5],
                ]
            );
    }
    
    public function actionSearch() { 
        return Article::find()->where(['like','title',$_POST['keyword']])->all();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
