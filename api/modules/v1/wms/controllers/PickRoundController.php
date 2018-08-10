<?php

namespace api\modules\v1\wms\controllers;
 
use yii\data\ActiveDataProvider; 
use common\models\Adminuser; 
use api\modules\v1\wms\controllers\CommonController;
use common\models\WmsPickRound; 

//这里可以作为restfull 普通业务的例子参考
class PickRoundController extends CommonController
{
    public $modelClass = 'common\models\WmsPickRound';
    
}
