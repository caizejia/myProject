<?php

return [
    // BaseObject 兼容 PHP7.2 （‘can't use 'Object’）
    'mdm\admin\components\Configs' => '@api/components/vendor/mdmsoft/yii2-admin/components/Configs.php',
    // 修改后台用户注册model signup
    'mdm\admin\models\form\Signup' => '@api/components/vendor/mdmsoft/yii2-admin/models/form/Signup.php',
    // 修改User
    'mdm\admin\models\User' => '@api/components/vendor/mdmsoft/yii2-admin/models/User.php',
    // 修改后台用户列表search model user
    'mdm\admin\models\searchs\User' => '@api/components/vendor/mdmsoft/yii2-admin/models/searchs/User.php',
    // mdm用户控制器改状态
    'mdm\admin\controllers\UserController' => '@api/components/vendor/mdmsoft/yii2-admin/controllers/UserController.php',
    // mdm路由models Route
    'mdm\admin\models\Route' => '@api/components/vendor/mdmsoft/yii2-admin/models/Route.php',
    // mdm路由models Assignment
    'mdm\admin\models\Assignment' => '@api/components/vendor/mdmsoft/yii2-admin/models/Assignment.php',
];
