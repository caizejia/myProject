<?php

namespace api\modules\v1\oms\controllers;

class OrderGoodsController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
