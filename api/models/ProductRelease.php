<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_product_release".
 *
 * @property int $id 产品发布表id
 * @property int $pid 产品表id
 * @property string $name 产品名称
 * @property string $country 国家2字码
 * @property string $sale_price 销售价格
 * @property string $price 虚拟价格
 * @property string $attr_list 属性json集合
 * @property string $img_list 图片集合
 * @property int $temp_type 模板类型（0默认 1泰国 2香港。。。）
 * @property string $domain 域名
 * @property string $host 二级域名
 * @property string $pro_param 产品参数
 * @property string $description 产品描述
 * @property int $is_sensitive 是否敏货 0：否；1：是
 * @property int $ads_user 投放人员id
 * @property int $create_by 创建人id
 * @property int $create_time 创建时间
 * @property int $update_time 更新时间
 * @property int $update_by 更新人
 * @property int $is_del 是否删除（0否 1是）
 */
class ProductRelease extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_product_release';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'temp_type', 'img_list', 'name', 'attr_list', 'country', 'domain', 'host', 'description', 'spu'], 'required', 'message' => "请输入{attribute}", 'on' => ['create']],
            [['pid', 'temp_type', 'is_sensitive', 'ads_user', 'create_by', 'create_time', 'update_time', 'update_by', 'is_del'], 'integer'],
            [['img_list'], 'string'],
            [['name', 'attr_list'], 'string', 'max' => 128],
            [['country'], 'string', 'max' => 2],
            [['domain'], 'string', 'max' => 32],
            [['host'], 'string', 'max' => 16],
            [['description'], 'string', 'max' => 256],
        ];
    }

    const SCENARIO_CREATE = 'create';

    const SCENARIO_UPDATE = 'update';

    /**
     * 配置验证场景
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['pid', 'img_list', 'temp_type', 'is_sensitive', 'ads_user', 'name', 'country', 'domain', 'host', 'description', 'attr_list']; 
        $scenarios[self::SCENARIO_UPDATE] = ['pid', 'img_list', 'temp_type', 'is_sensitive', 'ads_user', 'name', 'country', 'domain', 'host', 'description', 'attr_list']; 
        
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'name' => 'Name',
            'country' => 'Country',
            'sale_price' => 'Sale Price',
            'attr_list' => 'Attr List',
            'img_list' => 'Img List',
            'temp_type' => 'Temp Type',
            'domain' => 'Domain',
            'host' => 'Host',
            'description' => 'Description',
            'is_sensitive' => 'Is Sensitive',
            'ads_user' => 'Ads User',
            'create_by' => 'Create By',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'update_by' => 'Update By',
            'is_del' => 'Is Del',
        ];
    }

    /**
     * 发布产品对产品 一对一
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'pid']);
    }

    /**
     * 获取数据量
     */
    public function getCount($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取多条数据
     */
    public function getList($fields, $where, $limit = 20, $offset = 0)
    {
        $data = self::find()
            ->where($where)
            ->with('product')
            ->select($fields)
            ->limit($limit)
            ->offset($offset)
            ->asArray()->all();

        foreach ($data as &$v) {
            $v['img_list'] = json_decode($v['img_list'], true);
            $v['create_time'] = date('Y-m-d', $v['create_time']);
            $v['disable'] = $v['disable'] == 0 ? '未上架' : '上架';
        }

        return $data;
    }

    public static function getOne($fields, $where)
    {
        $data = self::find()
            ->where($where)
            ->select($fields)
            ->asArray()->one();

        return $data;
    }

    /**
     * 
     */
    public static function getDetail($id)
    {
        $data = self::find()->where(['is_del' => 0, 'id' => $id])
            ->asArray()->one();

        return $data;
    }

}
