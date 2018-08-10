<?php

namespace api\modules\v1\oms\controllers;

class ProblemsLogController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
