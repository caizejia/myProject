<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsInventory;
use common\models\WmsInventorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;
use yii\data\Pagination;
/**
 * InventoryController implements the CRUD actions for WmsInventory model.
 */
class InventoryController extends CommonController
{
    public $modelClass = 'common\models\WmsInventory';

//        $query = self::find()->where(['status' => 1]);
//
//        // 得到文章的总数（但是还没有从数据库取数据）
//        $count = $query->count();
//
//        // 使用总数来创建一个分页对象
//        $pagination = new Pagination(['totalCount' => $count]);
//
//        // 使用分页对象来填充 limit 子句并取得文章数据
//        $articles = $query->offset($pagination->offset)
//        ->limit($pagination->limit)
//        ->all();

    //一般搜索
    public function actionSearch() {
        return WmsInventory::find()
            ->where([
                'and',
                ['like','goods_id',$_POST['goods_id']],
                ['like','warehouse_id',$_POST['warehouse_id']]
             ])
            ->all();
    }
}
