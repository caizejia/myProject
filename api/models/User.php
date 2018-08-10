<?php

namespace api\models;

use Yii;
use api\components\Error;
use mdm\admin\components\DbManager;
use mdm\admin\components\Helper;
use yii\web\Cookie;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $realname
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token access token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * 用户ID与广告优化师代号对应表.
     *
     * @var array
     */
    public $uidToAds = [
        '31' => 'andy',
        '8' => 'humo',
        '49' => 'suwen',
        '32' => 'lu',
        '65' => 'py',
        '129' => 'cdj',
        '52' => 'lyw',
        '76' => 'evali',
        '81' => 'samantha',
        '82' => 'mario',
        '118' => 'wx',
        '116' => 'liang',
        '135' => 'Chloe',
        '88' => 'Summer',
        '148' => 'chenbf',
        '149' => 'MJ',
        '117' => 'zhu',
        '159' => 'edison',
        '169' => 'Whis',
        '180' => 'yangli',
        '204' => 'lhw',
        '209' => 'seed',
        '240' => 'XM',
        '95' => 'zf',
        '214' => 'olga',
        '246' => 'Lam',
        '101' => 'xij',
        '188' => 'smm',
        '97' => 'wyf',
        '98' => 'cxq',
        '100' => 'lsy',
        '56' => 'hqy',
        '96' => 'Allen',
        '242' => 'yiguang',
        '170' => 'Alan',
        '85' => 'Mandy',
        '86' => 'Victor',
        '168' => 'Alice',
        '33' => 'Jack',
        '236' => 'Sam',
        '215' => '周敏',
        '221' => 'hz',
        '194' => 'cpd',
        '196' => 'xc',
        '195' => 'guangjin',
        '87' => 'Lee',
        '162' => 'czy',
        '39' => 'mq',
        '90' => 'chj',
        '248' => 'zyy',
        '165' => 'xww',
        '62' => 'lqy',
        '208' => 'lfy',
        '206' => 'yjx',
        '84' => 'ian',
        '274' => 'ljn',
        '283' => 'spc',
        '278' => 'lrw',
        '282' => 'wangzehong',
        '286' => 'Gemin',
        '293' => 'fengsen',
        '290' => 'chl',
        '299' => 'YXG',
        '298' => 'xzp',
        '296' => 'suxin',
        '301' => 'gz',
        '275' => 'zengli',
        '304' => 'ZYQ',
        '307' => 'wjh',
        '292' => 'qx',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'realname', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'auth_key'], 'string', 'max' => 32],
            [['realname'], 'string', 'max' => 100],
            [['password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['access_token'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'realname' => 'Realname',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'access_token' => 'Access Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     *  获取单条数据.
     */
    public static function getLoginInfo($params)
    {
        $data = self::find()
            ->where(['username' => $params['username']])
            ->select(['id', 'username', 'password_hash', 'access_token'])
            ->asArray()->one();

        if ($data) {
            // 判断用户是否存在
            $hash = $data['password_hash'];
            if (Yii::$app->getSecurity()->validatePassword($params['password'], $hash)) {
                $help = new Helper();
                $routeList = array_keys($help->getRoutesByUser($data['id']));
                // 重新生成token 保存数据库
                $user = self::findOne($data['id']);
                $user->access_token = Yii::$app->security->generateRandomString();
                if(!$user->update()){
                    return Error::validError($user);
                }
                $res['access_token'] = $data['access_token'] = $user->access_token;
                // 设置登录状态
                self::setLoginStatus($data);
                // 获取用户权限目录菜单
                $menuList = include Yii::getAlias('@api/config').'/vue_menu_list.php';
                self::getMenuTree($menuList, $routeList);
                self::filterArrayIndex($menuList);
                $res['menu_list'] = $menuList;
                $res['route_list'] = $routeList;

                return $res;
            } else {
                return Error::errorJson(400, '密码错误');
            }
        } else {
            return Error::errorJson(400, '该用户不存在');
        }

        return $data;
    }

    /**
     * 设置登录状态信息
     */
    public static function setLoginStatus($data)
    {
        $session = Yii::$app->session;
        if (!$session->isActive) {
            $session->open();
        }
        $session->set('user', $data);
    }

    /**
     * 整理数组索引从0开始
     * @author YXH
     * @date 2018/07/11
     */
    public static function filterArrayIndex(&$arr)
    {
        $arr = array_values($arr);
        foreach ($arr as $k => &$v) {
            if (array_key_exists('subs', $v)) {
                self::filterArrayIndex($v['subs']);
            }
        }
    }

    /**
     * 获取vue菜单权限目录
     * @author YXH 
     * @date 2018/07/07
     */
    public static function getMenuTree(&$menuList, $routeList)
    {
        foreach ($menuList as $k => &$v) {
            if (array_key_exists('router', $v)) {
                // 判断是否有在route列表里面
                if(!in_array($v['router'], $routeList)){
                    unset($menuList[$k]);
                } 
                continue;
            }
            if (is_array($v['subs'])) {
                self::getMenuTree($v['subs'], $routeList);
            }
            if (!$v['subs']) {
                unset($menuList[$k]);
            }
        }
    }
}
