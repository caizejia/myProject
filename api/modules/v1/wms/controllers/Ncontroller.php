<?php
namespace api\modules\v1\wms\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query; 
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth; 
use yii\filters\auth\QueryParamAuth;

/**
 * //如果想不用restful 就 继承这个类， 如果用restful 就继承 CommonController
 */
class NController extends Controller
{
    //检查权限
    public function beforeAction($action)
    {

        if (!parent::beforeAction($action)) {
            return false;
        }
        $user = Yii::$app->user->id;
        if(Yii::$app->db->createCommand("select user_id from auth_assignment where item_name = 'admin' and user_id = '{$user}'")->queryOne()){
            return true;
        }
        $controller = $action->controller->id;
        $actionName = $action->id;
        if (Yii::$app->user->can($controller. '/*')) {
            return true;
        }
        if (Yii::$app->user->can($controller. '/'. $actionName)) {
            return true;
        }
        throw new \yii\web\UnauthorizedHttpException('对不起，您没有访问'. $controller. '/'. $actionName. '的权限');
        // return true;
    }

    //检查登录 多种方式验证token
    public function behaviors()
    { 
        $behaviors = parent::behaviors();
        
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBasicAuth::class,
                HttpBearerAuth::class,
                //HttpHeaderAuth::class,
                QueryParamAuth::class
            ]
        ]; 
 
        return $behaviors;
    }

    //包装数据成统一格式 $pagination为yii\data\Pagination
    public function wrapData($data,$pagination=false){
        $return = [];
        if($pagination){
            $_GET['page'] = isset($_GET['page'])? $_GET['page']:1;
            $_GET['per-page'] = isset($_GET['per-page'])? $_GET['per-page']:20;
            $return['_meta']['totalCount'] = $pagination->totalCount;
            $return['_meta']['pageCount'] = ceil($pagination->totalCount/$_GET['per-page']);
            $return['_meta']['currentPage'] = $_GET['page'];
            $return['_meta']['perPage'] = $_GET['per-page'];

        }
        $return['data'] = $data;
        return $return;
    }

    //返回统一错误格式
    //name 错误类型， message错误信息， status 对应http错误代码 （404,200）
    public function wrapError($name,$message,$status){ 
        $return = [
            "name" => $name,
            "message" => $message,
            "code" => 0,
            "status" => $status 
        ];
        return $return;
    }
}
