<?php

namespace api\modules\v1\wms\controllers;

use common\models\UploadForm;
use Yii;
use common\models\WmsUploads;
use common\models\WmsUploadsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;
use PHPExcel;

/**
 * WmsUploadsController implements the CRUD actions for WmsUploads model.
 */
class WmsUploadsController extends CommonController
{

    /**
     * Lists all WmsUploads models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WmsUploadsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WmsUploads model.
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
     * Creates a new WmsUploads model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WmsUploads();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WmsUploads model.
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
     * Deletes an existing WmsUploads model.
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
     * Finds the WmsUploads model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WmsUploads the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WmsUploads::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 文件导入
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionUpload()
    {
        $ret = ['status'=>500,'path'=>'','msg'=>'上传失败'];
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            $time = time();
            $id = Yii::$app->user->id;
            $model->file->saveAs('uploads/' .$time.'_'.$id. '.' . $model->file->extension);
            $filePath = 'uploads/' .$time.'_'.$id . '.' . $model->file->extension; // 要读取的文件的路径
//                $data = Excel::import($filePath,[
//                    'setFirstRecordAsKeys' => true,
//                    'setIndexSheetByName' => true,
//                    'getOnlySheet'=> 'sheet1',
//                ]);
//                $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//                $datas = json_decode($json);
            $purpose = Yii::$app->request->post('purpose');
            if(Yii::$app->db->createCommand()->insert("wms_uploads",[
                'user_id' => $id,
                'purpose' => $purpose,
                'file_name' => $filePath,
                'update_time' => date('Y-m-d H:i:s'),
                ])->execute()){ 
                $ret = ['status'=>0,'path'=>$filePath,'msg'=>'success'];
            } 
             
        }
        return $ret;
        //echo json_encode($ret);exit;
    }

    public function actionDownload(){
        if (Yii::$app->getRequest()->getMethod() == 'OPTIONS') {
            return true;
        }
        $id = Yii::$app->request->post('id');
        if($id){
            $message = Yii::$app->db->createCommand("select * from wms_excel_export where id = '{$id}'")->queryOne();
            if($message['user_id'] == Yii::$app->user->id) {
                $file_name = $message['result_name'];     //下载文件名
                $file_dir = Yii::$app->getBasePath() . '/web/download/';        //下载文件存放目录
                //检查文件是否存在
                if (!file_exists($file_dir . $file_name)) {
                    echo json_encode([
                        'code' => 400,
                        'msg' => '文件找不到',
                    ]);
                    exit ();
                } else {
                    //打开文件
                    $file = fopen($file_dir . $file_name, "r");
                    //输入文件标签
                    Header("Content-type: application/octet-stream");
                    Header("Accept-Ranges: bytes");
                    Header("Accept-Length: " . filesize($file_dir . $file_name));
                    Header("Content-Disposition: attachment; filename=" . $file_name);
                    //输出文件内容
                    //读取文件内容并直接输出到浏览器
                    echo fread($file, filesize($file_dir . $file_name));
                    fclose($file);
                    exit ();
                }
            }else{
                echo json_encode([
                    'code' => 400,
                    'msg' => '不是本人文件，禁止下载',
                ]);
            }
        }else{
            $file_name = Yii::$app->request->get('fileName').'.xlsx';     //下载文件名
            $file_dir = Yii::$app->getBasePath() . '/web/download/';        //下载文件存放目录
            //检查文件是否存在
            if (!file_exists($file_dir . $file_name)) {
                echo json_encode([
                    'code' => 400,
                    'msg' => '文件找不到',
                ]);
                exit ();
            } else {
                //打开文件
                $file = fopen($file_dir . $file_name, "r");
                //输入文件标签
                Header("Access-Control-Allow-Origin: *");
                Header("Access-Control-Allow-Headers: *");
                Header("Access-Control-Allow-Method: GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS");
                Header("Content-type: application/octet-stream");
                Header("Accept-Ranges: bytes");
                Header("Accept-Length: " . filesize($file_dir . $file_name));
                Header("Content-Disposition: attachment; filename=" . $file_name);
                //输出文件内容
                //读取文件内容并直接输出到浏览器
                echo fread($file, filesize($file_dir . $file_name));
                fclose($file);
                exit ();
            }
        }
    }
}
