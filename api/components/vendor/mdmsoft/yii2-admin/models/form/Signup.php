<?php
namespace mdm\admin\models\form;

use Yii;
use mdm\admin\models\User;
use yii\base\Model;

/**
 * Signup form
 */
class Signup extends Model
{
    public $username;
    public $email;
    public $password;
    public $dingding_id;
    public $warehouse_id;

    public $realname;// add by yxh 2018/07/12

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => 'mdm\admin\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            // add by yxh 2018/07/12
            ['realname', 'filter', 'filter' => 'trim'],
            ['realname', 'required'],
            ['realname', 'string', 'min' => 2, 'max' => 255],
            ['dingding_id', 'required'],
            ['warehouse_id', 'required'],
            // -- end --
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => 'mdm\admin\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realname' => '姓名',
            'username' => '用户名',
            'email' => '邮箱',
            'password' => '密码',
            'dingding_id' => '钉钉ID',
            'warehouse_id' => '仓库ID',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->realname = $this->realname;// add by yxh 2018/07/12
            $user->email = $this->email;
            $user->dingding_id = $this->dingding_id;
            $user->warehouse_id = $this->warehouse_id;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }
}
