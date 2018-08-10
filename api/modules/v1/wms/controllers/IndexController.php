<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl; 
use common\models\WmsSoBill; 
/**
 * Site controller   免登陆状态的 操作（打印面单）
 */
class IndexController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    public function beforeAction($action)
    {
       return Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
    }
  


    public function actionThSinglePlane(){
        $this->layout = false;
        $id = Yii::$app->request->get('id');
        $mpdf = new \mPDF('th', [100, 150], 0, '', 1, 1, 1, 1);
        $mpdf->useAdobeCJK = false;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showWatermarkText = false;
        //原来的html页面
        $mpdf->WriteHTML($this->renderPartial("th-single-plane", [
            'id' => $id,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

    public function actionThSinglePlaneBjt(){
        $this->layout = false;
        $id = Yii::$app->request->get('id');
        $mpdf = new \mPDF('zh', [100, 100], 0, '', 1, 1, 1, 1);
        $mpdf->useAdobeCJK = false;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showWatermarkText = false;
        //原来的html页面
        $mpdf->WriteHTML($this->renderPartial("th-single-plane-bjt", [
            'id' => $id,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

    public function actionThSinglePlaneCds(){
        $this->layout = false;
        $id = Yii::$app->request->get('id');
        $mpdf = new \mPDF('zh', [100, 100], 0, '', 1, 1, 1, 1);
        $mpdf->useAdobeCJK = false;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showWatermarkText = false;
        //原来的html页面
        $mpdf->WriteHTML($this->renderPartial("th-single-plane-cds", [
            'id' => $id,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

    /**
     * 顺丰面单
     * @throws \MpdfException
     */
    public function actionSfSinglePlane(){
        $this->layout = false;
        $id = Yii::$app->request->get('id');
        $mpdf = new \mPDF('zh-CN', [100, 180], 0, '', 2, 2, 2, 2);
        $mpdf->useAdobeCJK = false;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showWatermarkText = false;
        //原来的html页面
//        return $this->render('sf',['id' => $id,]);
        $mpdf->WriteHTML($this->renderPartial("sf-single-plane", [
            'id' => $id,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

    /**
     * 生成面单
     */
    public function actionSinglePlane()
    {
        $id = Yii::$app->request->get('id');
        $pdf = Yii::$app->db->createCommand("select * from orders where id = $id")->queryOne();
        $mpdf = new mPDF('zh-CN', [100, 150], 0, '', 1, 1, 1, 1);
        $mpdf->useAdobeCJK = true;
        //原来的html页面
        $mpdf->WriteHTML($this->renderPartial("single-plane", [
            'pdf' => $pdf,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

    /**
     * 生成面单
     */
    public function actionSinglePlaneTh()
    {
        $this->layout = false;
        $id = Yii::$app->request->get('id');
        $mpdf = new \mPDF('tha', [100, 150], 0, '', 1, 1, 1, 1);
        $mpdf->useAdobeCJK = false;
        $mpdf->SetDisplayMode('fullpage');
//        $mpdf->showWatermarkText = false;
        //原来的html页面
//        return $this->render("single-plane-th", [
//            'id' => $id,
//        ]);
        $mpdf->WriteHTML($this->renderPartial("single-plane-th", [
            'id' => $id,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

    /**
     * 生成面单
     */
    public function actionSinglePlaneA()
    {
        $this->layout = false;
        $id = Yii::$app->request->get('id');
        $pdf = Yii::$app->db->createCommand("select * from orders where id = $id")->queryOne();
        $mpdf = new mPDF('zh-CN', [100, 150], 0, '', 1, 1, 1, 1);
        $mpdf->useAdobeCJK = true;
        //原来的html页面
        $mpdf->WriteHTML($this->renderPartial("single-plane-a", [
            'pdf' => $pdf,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

    /**
     * 生成面单
     */
    public function actionTwSinglePlane()
    {
        $id = Yii::$app->request->get('id');
        $pdf = Yii::$app->db->createCommand("select * from orders where id = $id")->queryOne();
        $mpdf = new mPDF('zh-CN', [100, 150], 0, '', 1, 1, 1, 1);
        $mpdf->useAdobeCJK = true;
        //原来的html页面
        $mpdf->WriteHTML($this->renderPartial("tw-single-plane", [
            'pdf' => $pdf,
        ]));
        //保存名称
        $mpdf->Output('MyPDF', 'I');
    }

}
