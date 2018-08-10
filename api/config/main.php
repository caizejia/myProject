<?php
// 参数配置
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

// 路由规则配置
$rules = array_merge(
    include 'oms_route.php',
    include 'wms_route.php'
);

$config = [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    // 配置语言
    'language' => 'zh-CN',
    // 配置时区
    'timeZone' => 'Asia/Chongqing',
    // -------- RBAC SETUP --------
    "aliases" => [    
        "@mdm/admin" => "@vendor/mdmsoft/yii2-admin",
    ],
    // --------- END -----------
    // 模块配置
    'modules' => [
        // oms使用自定义restful 所以定义oms 在映射时定义v1
        'oms' => [
            'class' => 'api\modules\v1\oms\Module',
        ],
        // wms使用yii2自带restful 所以命名v1用在url上
        'v1' => [
            'class' => 'api\modules\v1\wms\Module',
        ],
        // -------- RBAC SETUP --------
        'admin' => [
            'class' => 'mdm\admin\Module',
            //'layout' => 'left-menu',//yii2-admin的导航菜单
        ],
        // -------- END --------
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'cookieValidationKey' => 'EohseCY4X-iQ0a55Qu4VKmXySXIc0Hhv',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        // -------- RBAC SETUP --------
        'user' => [ 
            'identityClass' => 'mdm\admin\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],  
            'loginUrl' => ['admin/user/login'],
        ],
        // -------- END ----------
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'api\components\WmsErrorHandler',
            //'errorAction' => 'site/error',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.11.247;dbname=orkoerp',
            'username' => 'root',
            'password' => 'dddddd',
            'charset' => 'utf8',
        ],
        'db2' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.11.247;dbname=orkotech_com',
            'username' => 'root',
            'password' => 'dddddd',
            'charset' => 'utf8',
        ],
        'urlManager' => [
           'enablePrettyUrl' => true,
           'showScriptName' => false,
           //'enableStrictParsing' => true,
           'rules' => $rules,
        ],
        // i18n语言配置
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'zn-CN',
                    'fileMap' => [
                        'common' => 'common.php',
                        'msg' => 'msg.php',
                    ],
                ],
            ],
        ],
        // -------- RBAC SETUP --------
        'authManager' => [
            'class' => 'yii\rbac\DbManager', 
            //'defaultRoles' => ['未登录用户'],// 添加此行代码，指定默认规则为 '未登录用户'
        ],
        // ---------- END ---------
        'view' => [
            'theme' => [
                'pathMap' => [
                    // 后台注册模板 映射到 api/views 目录
                    '@vendor/mdmsoft/yii2-admin/views/user' => '@api/views/mdmsoft/user',
                    // 修改新增的 data 数据会提示错误
                    '@vendor/mdmsoft/yii2-admin/views/item' => '@api/views/mdmsoft/item',
                ],
            ],
        ],
    ],
    // -------- RBAC SETUP --------
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'v1/*', 
            'oms/*', 
            'debug/*', 
        ],
    ],
    // ---------- END ---------

    'params' => $params,
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['192.168.10.141', '::1','127.0.0.1','192.168.0.172'],
    ];
}

return $config;
