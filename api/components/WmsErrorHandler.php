<?php
namespace api\components;

use Yii;
use yii\web\ErrorHandler;
use yii\base\Exception;

//此组件目的是为了规范统一错误返回格式
//使用方法： $config['components']['errorHandler']['class'] = 'api\components\WmsErrorHandler';
/**
 * 异常捕获器
 */
class WmsErrorHandler extends ErrorHandler
{
    /**
     * [renderException description]
     * @property  $exception | Object 异常数据对象
     * 
     */
    public function renderException($exception)
    { 
        // 获取异常数据
        $code = $exception->statusCode ?? 500;  
        method_exists($exception,'getMessage')  ? $message  = $exception->getMessage()  : $message = '';
        method_exists($exception,'getName')     ? $name     = $exception->getName()     : $name = '';
        method_exists($exception,'getFile')     ? $file     = $exception->getFile()     : $file = '';
        method_exists($exception,'getLine')     ? $line     = $exception->getLine()     : $line = '';
        method_exists($exception,'getTraceAsString') ? $traceString = $exception->getTraceAsString() : $traceString = '';
        $time    = time();
        $ip      = Yii::$app->request->userIP;
        $url     = Yii::$app->request->getUrl();//Yii::$service->url->getCurrentUrl();
        $method     = Yii::$app->request->getMethod();
        $req_info = $this->getRequestInfo();
        $reponse = Yii::$app->response;
        Yii::$app->response->format = $reponse::FORMAT_JSON;
        Yii::$app->response->statusCode = $code; //这里保留http状态码，看情况取舍
        if (YII_ENV_PROD) {
            $errorKey = $this->saveProdException($code, $message, $file, $line, $time, $ip, $name, $traceString, $url, $req_info);
            Yii::$app->response->data = [
                'request' => $url,
                'method' => $method,
                'code'      => $code,
                'error'  => $message,
            ];
            Yii::$app->response->send();
            Yii::$app->end();
        } else {
            $time    = date('Y-m-d H:i:s', $time);
            $exceptionInfo = [
                'request' => $url,
                'method' => $method,
                'code'      => $code, 
                'error'   => $message,
                'file'      => $file,
                'line'      => $line,
                'time'      => $time,
                'ip'        => $ip,
                'name'      => $name,
                'traceString' => $traceString,
            ];
            Yii::$app->response->data = $exceptionInfo;
            Yii::$app->response->send();
            Yii::$app->end();
        }
    }
    
    public function getRequestInfo(){
        $request = Yii::$app->request;
        $ajax = 0;
        $request_type = '';
        $request_data = [];
        $header_accept = '';
        $header_user_agent = '';
        if ($request->isAjax) { 
            $ajax = 1;
        }
        if ($request->isGet)  { 
            $request_type = 'get';
            $request_data = $request->get();
        }
        if ($request->isPost) { 
            $request_type = 'post';
            $request_data = $request->post();
        }
        // $headers is an object of yii\web\HeaderCollection 
        $headers = $request->getHeaders();
        $headers_arr = [];
        if (is_object($headers) or is_array($headers)) {
            foreach ($headers as $k=>$v) {
                $headers_arr[$k] = $v;
            }
        }
        $userHost   = Yii::$app->request->userHost; 
        $userIP     = Yii::$app->request->userIP;
        return [
            'ajax'              => $ajax,
            'request_type'      => $request_type,
            'request_data'      => $request_data,
            'headers_data'      => $headers_arr,
            'userHost'          => $userHost,
            'userIP'            => $userIP,
        ];
    }
     
    public function saveProdException($code, $message, $file, $line, $created_at, $ip, $name, $trace_string, $url, $req_info){
        return true; 
        /*return Yii::$service->helper->errorHandler->saveByErrorHandler(
            $code, $message, $file, $line, $created_at,
            $ip, $name, $trace_string, $url, $req_info
        );*/
        
    }
    /**
     * 这块代码目前没有编写，您也可以用 Sentry（错误日志收集框架） 来收集错误日志。
     * $option = [
     *   'fromMail' => 'xxx@xxx.com',
     *   'subject'  => 'fecshop报错 Code:' . $exceptionInfo['code'],
     *   'htmlBody' => '异常' . '(' . YII_ENV . ')' . $exceptionInfo['code'] . ':' . $exceptionInfo['message'] . '<br />文件：' . $exceptionInfo['file'] . ':' . $exceptionInfo['line'] . '<br /> 时间：' . $exceptionInfo['time'] . '<br />请求ip：' . $exceptionInfo['ip'],
     * ];
     *
     *
     *if (!$result) {
     *    throw new Exception("Exception mail send faild!", 1);
     *}
     */
}
