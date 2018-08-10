<?php

namespace api\components;

use Yii;
use api\models\User;
use api\components\Error;
use mdm\admin\components\Helper;
use yii\web\Cookie;

//行为类需要继承Behavior
class AuthBehavior extends \yii\base\ActionFilter
{

    //重载beforeAction方法
    public function beforeAction($action)
    {
        // 访问用户类别
        $userType = Yii::$app->request->headers->get('Access-User-Type') ?? 1;
        if ($userType == 1) {// 内部用户
            // 验证access_token是否存在
            $flag = $this->checkToken($action);
            $this->routeAuth($action);
            return true;
            return $flag;
        } else {// 下单用户
            return $this->checkSkey();
        }
    }

    /**
     * 检查access_token
     */
    public function checkToken($action)
    {
        $accessToken = trim(Yii::$app->request->headers->get('Access-Token'));
        if ($accessToken) {
            $session = Yii::$app->session;
            $session->open();
            $user = $session->get('user') ?? false;
            if ($user) {// 有session先检测session
                if ($user['access_token'] == $accessToken) {
                    $action->controller->uid = $user['id'];
                    // 初始化赋值uid
                    return true;
                } else {
                    $return = Error::errorJson(401);
                    header('HTTP/1.1 401 Unauthorized');
                    echo json_encode($return);exit;
                }
            } else {// 没session检测数据表数据
                $data = User::find()
                ->where(['access_token' => $accessToken])
                ->select(['id', 'username', 'access_token'])
                ->asArray()->one();
                if (!$data) {
                    $return = Error::errorJson(499, 'Token 过期或无效'); 
                    header('HTTP/1.1 499 Token error');
                    echo json_encode($return);exit; 
                } else {// 重新设置session
                    $session->set('user', $data);
                    $action->controller->uid = $data['id'];
                    // 初始化赋值uid
                    return true;
                }
            }
        } else {
            $return = Error::errorJson(401, 'Access-Token Not Found');
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode($return);exit;
        }
    }

    /**
     * 检查路由权限
     */
    public function routeAuth($action)
    {
        $route = $action->controller->module->module->requestedRoute;
        $help = new Helper();
        $allowList = array_keys($help->getRoutesByUser($action->controller->uid));
        if (!in_array('/'.$route, $allowList)) {
            $return = Error::errorJson(401);
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode($return);exit;
        }
    }

}
