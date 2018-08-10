<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_member".
 *
 * @property string $id
 * @property string $name 姓名
 * @property string $phone 手机号
 * @property string $county 国家2字码
 * @property int $phone_check 手机验证 0未验证 1验证
 * @property string $address 地址
 * @property string $ip 用户登陆ip
 * @property int $create_time
 * @property int $identity 身份 0正常 1黑名单 2 VIP用户
 */
class Member extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'county', 'address', 'ip', 'create_time'], 'required'],
            [['create_time'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
            [['county'], 'string', 'max' => 2],
            [['phone_check', 'identity'], 'string', 'max' => 1],
            [['address'], 'string', 'max' => 256],
            [['ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'county' => 'County',
            'phone_check' => 'Phone Check',
            'address' => 'Address',
            'ip' => 'Ip',
            'create_time' => 'Create Time',
            'identity' => 'Identity',
        ];
    }
}
