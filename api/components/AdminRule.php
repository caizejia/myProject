<?php

namespace api\components;

use Yii;
use yii\rbac\Rule;

class AdminRule extends Rule
{
    public $name = 'admin';

    public function execute($user, $item, $params)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        if (!$id) {
            return false;
        }

        $model = Brands::findOne($id);
        if (!$model) {
            return false;
        }

        $username = Yii::$app->user->identity->username;
        $role = Yii::$app->user->identity->role;
        if ($role == User::ROLE_ADMIN || $username == $model->operate) {
            return true;
        }

        return false;
    }
}
