<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_customer".
 *
 * @property string $id 用户自增id
 * @property string $name 用户名
 * @property string $phone 手机号
 * @property string $email email邮箱
 * @property string $county 国家
 * @property string $city 城市
 * @property string $district 区
 * @property string $address 地址
 * @property string $last_ip 最近一次ip
 * @property int $lv 客户等级 0普通 1VIP 2黑名单
 * @property string $id_img 身份证图片
 * @property string $create_time 创建时间
 * @property int $is_del 是否删除（0否 1是）
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city', 'district', 'address', 'id_img'], 'required'],
            [[ 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 16],
            [['phone'], 'string', 'max' => 20],
            [['email', 'id_img'], 'string', 'max' => 128],
            [['county', 'district', 'address'], 'string', 'max' => 255],
            [['city'], 'string', 'max' => 50],
            [['lv', 'is_del'], 'string', 'max' => 1],
            [['last_ip'], 'string', 'max' => 15],
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
            'email' => 'Email',
            'county' => 'County',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'last_ip' => 'Last Ip',
            'lv' => 'Lv',
            'id_img' => 'Id Img',
            'create_time' => 'Create Time',
            'is_del' => 'Is Del',
        ];
    }
}
