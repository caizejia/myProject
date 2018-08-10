<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\WmsPurchases;
use common\models\WmsPurchasesSearch;
use common\models\WmsPurchasesDetail;
use api\modules\v1\wms\controllers\CommonController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PurchasesController implements the CRUD actions for Purchases model.
 */
class PurchasesController extends CommonController
{
    public $modelClass = 'common\models\WmsPurchases';
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
//        ];
//    }

    /**
     * @return array
     * @重写设置
     */
    public function actions()
    {
        $actions = parent::actions();
        // 禁用"delete" 和 "create" 动作
//        unset($actions['delete']);
        unset($actions['create']);
//        unset($actions['update']);
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    /**
     * Lists all Purchases models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WmsPurchasesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        foreach ($dataProvider->getModels() as $value)
        {
            $data[] = [
                'id' => $value['id'],
                'ref' => $value['ref'],
                'supplier_id' => $value['supplier_id'],
                'supplier_name' => $value['supplier_name'],
                'supplier_platform' => $value['supplier_platform'],
                'create_time' => $value['create_time'],
                'status' => WmsPurchases::$status[$value['status']],
                'memo' => $value['memo'],
            ];
        }
        $data = $this->wrapData($data,$dataProvider->pagination);

        return $data;
    }

    /**
     * Displays a single Purchases model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Purchases model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        date_default_timezone_set("Asia/Shanghai");
        //获取表单信息
//        print_r(Yii::$app->request->post());die;
        $post_data = Yii::$app->request->post();

        $cache = Yii::$app->cache;
        //拼接系统采购单号
        if ($cache->exists('n_ref'))
        {
            $n = $cache->get('n_ref');
        }else{
            $n = sprintf("%02d", 1);
        }
        $post_data['ref'] = date('YmdHis') . $n;
        $n = sprintf("%02d", ($n += 1));
        $cache->set('n_ref',$n);
        //供应商ID
        $supplier_name = trim($post_data['supplier_name']);
        $supplier_id = Yii::$app->db->createCommand('SELECT id FROM wms_supplier WHERE supplier_name =' . $supplier_name)->queryScalar();
        $post_data['supplier_id'] = $supplier_id;
        //生成采购单时间
        $post_data['create_time'] = date('Y-m-d H:i:s');

        $result = Yii::$app->db->createCommand()->insert('wms_purchases', $post_data)->execute();

        if ($result) {
            return ['code' => 200, 'msg' => '新增成功'];
        } else {
            return ['code' => 500, 'msg' => '新增失败'];
        }
    }

    /**
     * Updates an existing Purchases model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Purchases model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Purchases model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Purchases the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WmsPurchases::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
