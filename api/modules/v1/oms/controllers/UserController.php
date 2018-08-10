<?php

namespace api\modules\v1\oms\controllers;

use yii\web\Controller;
use Yii;
use api\models\User;
use api\components\Error;
use yii\web\Response;
use mdm\admin\components\DbManager;

/**
 * User controller for the `v1` module.
 */
class UserController extends Controller
{
    // 关闭csrf验证
    public $enableCsrfValidation = false;

    /**
     * @fun 获取用户access_token
     *
     * @return string
     */
    public function actionToken()
    {
        header("Access-Control-Allow-Origin: *");
        // 设置返回json格式
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $res = User::getLoginInfo($params);

        return $res;
    }
}
