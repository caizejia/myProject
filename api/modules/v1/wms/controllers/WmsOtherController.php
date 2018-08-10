<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsOther;
use common\models\WmsOtherSearch;
use yii\web\Controller;
use api\modules\v1\wms\controllers\CommonController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WmsOtherController implements the CRUD actions for WmsOther model.
 */
class WmsOtherController extends CommonController
{
//    public function actions()
//    {
//        $actions = parent::actions();
//        // 禁用"delete" 和 "create" 动作
////        unset($actions['delete']);
////        unset( $actions['create']);
//        unset($actions['update']);
////        unset($actions['index']);// 以下重写了原来的 index
//        return $actions;
//    }

    public $modelClass = 'common\models\WmsOther';

//    public function actionUpdate($id){
//        $model = WmsOther::findOne($id);
//        //获取表单信息
//        $post_data = Yii::$app->request->post();
////        var_dump($post_data);die;
//        $result= $model->save($post_data);
//        if ($result) {
//            return $post_data;
//        }
//        return ['code'=>500,'error'=>'put fail'];
//    }
}
