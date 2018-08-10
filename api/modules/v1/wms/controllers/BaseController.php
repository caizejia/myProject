<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use yii\web\Response;  

class BaseController extends \yii\web\Controller
{
    // 关闭csrf验证
    public $enableCsrfValidation = false;

    // 用户id 在AuthBehavior生成
    public $uid;

    /**
     * 初始化方法 访问时第一步到Init方法.
     */
    public function init()
    {
        parent::init();
        // 设置默认返回json格式数据
        Yii::$app->response->format = Response::FORMAT_JSON;
    }


    /**
     * 加载自定义行为 访问时第二步执行auth 事件.
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // 配置跨域访问域名
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400,
                ],
            ], 
        ]);
    }

    /**
     * 合并响应成功状态码 200.
     *
     * @remark 并不是返回json格式 只是加了请求成功的状态码
     *
     * @return fix
     */
    public function jsonReturn($data = '', $msg = '')
    {
        return ['code' => 200, 'data' => $data, 'msg' => $msg, 'error' => $msg];
    }

    /**
     * 处理vue预检请求options
     */
    public function actionOptions()
    { 
        return true;
    }
}
