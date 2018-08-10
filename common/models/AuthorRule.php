<?php
namespace common\models;

use yii\rbac\Rule;// execute

use Yii;

//这个是作为 rbac 的规则 例子
class AuthorRule extends Rule
{
    
    public $name = "isAuthor";

    public function execute($user, $item, $params)
    {
        $action = Yii::$app->controller->action->id;

        if ($action == 'delete') {
            $cateid = Yii::$app->request->get("id");
            $cate = Article::findOne($cateid);
            return $cate->adminid == $user;
        }

        return true;

    }

}
