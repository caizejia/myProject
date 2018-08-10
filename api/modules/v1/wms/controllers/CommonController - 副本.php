<?php

namespace api\modules\v1\wms\controllers;

use Yii;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
//use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

//如果想用restful 就 继承这个类， 如果不用restful 就继承 Ncontroller
class CommonController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        //换个返回格式的方法 ： https://stackoverflow.com/questions/35453780/how-do-i-override-the-rest-serializer-in-yii2
        'collectionEnvelope' => 'data', //配置返回格式
    ];
    
    //检查权限
    public function beforeAction($action)
    {  
        if (!parent::beforeAction($action)) { //执行下面的 behaviors
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
        /*if (Yii::$app->getRequest()->getMethod() == 'OPTIONS') { 
            $this->options();
            return true; 
        } */
        if($actionName =='options'){
            return true;
        } 
        
        
        throw new \yii\web\UnauthorizedHttpException('对不起，您没有访问'. $controller. '/'. $actionName. '的权限');
        // return true;
    }

    //检查登录 多种方式验证token
    public function behaviors()
    {   
        $behaviors = parent::behaviors();
 
        $behaviors =  ArrayHelper::merge([
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    //'Origin' => ['http://localhost:1024'],
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Origin' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => ['Authorization','access-token'],
                ],
            ],
        ], $behaviors);

        if (Yii::$app->getRequest()->getMethod() == 'OPTIONS') {
            //return $behaviors;
         }

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBasicAuth::class,
                HttpBearerAuth::class,
                //HttpHeaderAuth::class,
                QueryParamAuth::class
            ]
        ];
        $behaviors['authenticator']['except'] = ['options'];



//        $behaviors['authenticator']['except'] = ArrayHelper::merge($behaviors['authenticator']['except'],
//            [
//                'access-token','options'
//            ]
//        );

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

    public function init()
    {
        // var_dump(Yii::$app->controller);
        // exit;
        // 获取当前用户要访问的控制器名称和方法名称 index.php?r=admin/user/del
        // category/* caretory/add
        /*if (Yii::$app->session['admin']['isLogin'] != 1) {
            return $this->redirect(['/admin/public/login']);
        }*/
    }


    /*public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        parent::beforeAction($action);

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            // End it, otherwise a 401 will be shown.
            Yii::$app->end();
        }

        return true;
    }*/
    public function options()
    {
        echo '8';exit;
         return true;
    }
}
