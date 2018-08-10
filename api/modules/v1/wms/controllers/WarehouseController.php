<?php
namespace api\modules\v1\wms\controllers;
 
use yii\data\ActiveDataProvider; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsWarehouse; 

//这里可以作为restfull 普通业务的例子参考
class WarehouseController extends CommonController
{
    public $modelClass = 'common\models\WmsWarehouse';
    
}