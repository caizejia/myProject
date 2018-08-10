<?php
namespace api\modules\v1\wms\controllers;
 
use common\models\WmsInventory1;
use yii\data\ActiveDataProvider;
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsInventory1Search;

//这里暂时没用，作为 转运仓出入库的旧系统代码
class DoStocksController extends CommonController
{
    public $modelClass = 'common\models\WmsInventory1';
    
    public function beforeAction($action)
    {
       return Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
    }
    
    
     //////////////////以下流程为海外仓转运（出库和入库）//////////////////////////////////
    //具体流程：收件-》扫描原运单号上架-》新订单匹配转运单-》批量打印转运拣货单-》根据库位号，原运单号找货-》扫描老运单号，打印新运单-》扫描出库
    //暂时假设是返回html，没有做成api形式 TODO
    /**
     * 1，海外退件-扫描库位
     */
    public function actionReturnsKw(){
        $this->layout = false;
        return $this->render('returns-kw');
    }
    /**
     *,2，海外退件-扫描物流单号（收货）
     */
    public function actionReturnsLcNumber(){
        $this->layout = false;
        $id = Yii::$app->request->get('id');
        return $this->render('returns-lc-number',[ 'id' => $id ]);
    }
    /**
     *,3，海外退件-检查物流单号（收货）
     */
    public function actionReturnsLcCheck(){
        $id = Yii::$app->request->post('id');
        $lc_number = Yii::$app->request->post('lc');
        $orders = new WmsSoBill();
        $order = WmsSoBill::find()->andWhere(['=','lc_number',$lc_number])->one();
        if($order){
            if(($orders->getLcV)[Yii::$app->user->id] == $order->lc){
                echo json_encode([
                    'res' => true,
                    'msg' => '正确',
                ]);
            }else{
                echo json_encode([
                    'res' => false,
                    'msg' => '该单号不是本服务商的',
                ]);
            }
        }else{
            echo json_encode([
                'res' => false,
                'msg' => '该物流单号不存在',
            ]);
        }
    }

 
    
}