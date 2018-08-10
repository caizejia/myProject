<?php
/**
 * 错误处理类.
 * 
 * @author YXH
 *
 * @version 1.0
 *
 * @copyright (c) 2018,
 */

namespace api\components;

use Yii;

class Error
{
    /**
     * 公共的错误返回处理,通过传入参数,返回对应的错误代码.
     *
     * @param $code
     *
     * @return array
     */
    public static function errorJson($code, $msg = '')
    {
        $requests = Yii::$app->request; //返回值

        //带入记录错误代码的文件
        $errorFile = include \Yii::$app->basePath.'/components/error/ErrorCode.php';
        //获取http状态码,以及文字说明
        $errorInfo = $errorFile["$code"];

        $httpCode = $errorInfo['http_code'];
        $errorText = $errorInfo['remark'];
        if ($msg) {// 自定义提示信息msg
            $errorText = $msg;
        }

        $error_body = [ //设置返回的格式
            'request' => $requests->getUrl(),
            'method' => $requests->getMethod(),
            'code' => $code,
            'error' => $errorText,
        ];

        $response = Yii::$app->response;
        $response->statusCode = $httpCode;
        $headers = Yii::$app->response->headers;
        $headers->add('X-Halo-Result', 0);

        return $error_body;
    }

    /**
     * 数据入库前验证错误返回
     */
    public static function validError($model)
    {
        // 参数验证失败
        $error = $model->errors;
        if (empty($error)) {
            $msg = '没有传参，无法验证数据';
        } else {
            $msg = '';
        }
        foreach ($error as $key) {
            $msg .= $key[0].' ';
        }

        return self::errorJson(403, $msg);
    }
}
