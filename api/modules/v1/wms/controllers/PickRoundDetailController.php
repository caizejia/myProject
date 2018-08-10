<?php

namespace api\modules\v1\wms\controllers;
 
use yii\data\ActiveDataProvider; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsPickRoundDetail; 

//这里可以作为restfull 普通业务的例子参考
class PickRoundDetailController extends CommonController
{
    public $modelClass = 'common\models\WmsPickRoundDetail';

    public function actionBill($id){
        return WmsPickRoundDetail::find()->where(['pick_round_id'=>$id])->all();
    }
}