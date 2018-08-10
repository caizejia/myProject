<?php
namespace api\modules\v1\wms\controllers;
 
use yii\data\Pagination;  
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsInventoryDetail; 
use common\models\WmsInventoryDetailSearch; 
use common\models\Adminuser; 


//这里可以作为 非restfull 普通业务的例子参考
class InventoryDetailController extends CommonController
{
    public $modelClass = 'common\models\WmsInventoryDetail';
      

    public  function actions()
    {
        $actions = parent::actions();
        // 禁用"delete" 和 "create" 动作
//        unset($actions['delete']);
//        unset( $actions['create']);
//        unset($actions['update']);
        unset($actions['index']);// 以下重写了原来的 index
        return $actions;
    }

    
    public function actionIndex()
    { 
        $searchModel =  new WmsInventoryDetailSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams); 
        return $dataProvider;
    }

    //最简单api例子  查询列表
    public function actionIndex0()
    {
        // 创建一个 DB 查询来获得所有 goods_id  的 库存出入库明细
        $query = $this->modelClass::find()->where(['goods_id' => $_GET['goods_id']]);
        //'0 库存建账'、'1 采购入库'、'2 采购退货出库'、'3 销售出库'、'4 销售退货入库'、'5 库存盘点-盘盈入库'、'6 库存盘点-盘亏出库'
        if(isset($_GET['ref_type']) ){
            $query->where(['ref_type' => $_GET['ref_type']]);
        }
        // 得到明细的总数（但是还没有从数据库取数据）
        $count = $query->count();
        // 使用总数来创建一个分页对象
        $pagination = new Pagination(['totalCount' => $count]);
        // 使用分页对象来填充 limit 子句并取得文章数据
        $data = $query->offset($pagination->offset)
        ->limit($pagination->limit) 
        ->asArray()
        ->all();
        //数据人性化处理
        foreach ($data as $key => &$value) {
            $value['ref_type'] = $this->modelClass::$ref_type[$value['ref_type']];
            if($value['action_user_id']){
                $user = Adminuser::findIdentity($value['action_user_id']);
                $value['action_user_name'] = $user['username'];
            }else{
                $value['action_user_name'] = '系统';
            }
            
        }
 
        return $this->wrapData($data,$pagination); //包装一下数据格式
    }

    //一般搜索
     public function actionSearch() {
        $data = $this->modelClass::find()->where(['like','goods_id',$_GET['keyword']])->asArray()->all();
        return $this->wrapData($data); 
    }



    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
