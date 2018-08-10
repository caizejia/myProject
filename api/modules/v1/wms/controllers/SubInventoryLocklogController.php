<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use common\models\SubInventoryLocklog;
use common\models\SubInventoryLocklogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\modules\v1\wms\controllers\CommonController;

/**
 * SubInventoryLocklogController implements the CRUD actions for SubInventoryLocklog model.
 */
class SubInventoryLocklogController extends CommonController
{
    public $modelClass = 'common\models\SubInventoryLocklog';
}
