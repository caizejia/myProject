<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_product_t".
 *
 * @property int $id 产品表id
 * @property string $name 产品名称
 * @property int $cid 产品分类id
 * @property int $bid 产品品牌id
 * @property string $sid_list 供应商id集合（1,4,5）
 * @property string $spu_code SPU码
 * @property int $is_sensitive 是否敏感（0否 1是）
 * @property int $sex 适用性别（0通用 1男 2女）
 * @property string $cost 成本
 * @property string $description 产品描述
 * @property string $attr_list 属性集合
 * @property string $think 选品思路
 * @property string $declare_cname 中文申报名
 * @property string $declare_ename 英文申报名
 * @property string $declare_price 申报价格
 * @property string $declare_code 申报编码
 * @property int $create_time 创建时间
 * @property int $create_by 创建人
 * @property int $update_time 更新时间
 * @property int $update_by 更新人
 * @property int $is_del 是否删除（0否 1是）
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * 产品可见程度
     */
    const LEVEL_LIST = [
        '所有人可见',
        '组内可见',
        '自己可见',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'cid', 'bid', 'sid_list', 'is_sensitive', 'sex', 'cost', 'description', 'attr_list', 'think', 'declare_cname', 'declare_ename', 'declare_price', 'declare_code', 'sale_price', 'price'], 'required', 'message' => "请输入{attribute}" , 'on' => ['create']],
            [['cid', 'bid', 'is_sensitive', 'sex', 'create_time', 'create_by', 'update_time', 'update_by', 'is_del'], 'integer'],
            [['cost', 'declare_price'], 'number'],
            [['name', 'description', 'attr_list', 'think'], 'string', 'max' => '512'],
            [['sid_list'], 'string', 'max' => 64],
            [['spu_code'], 'string', 'max' => 16],
            [['declare_cname', 'declare_ename', 'declare_code'], 'string', 'max' => 32],
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
        $scenarios[self::SCENARIO_CREATE] = [
            'name', 'keyword', 'cid', 'bid', 'is_sensitive', 'sex', 'cost', 
            'declare_cname', 'declare_ename', 'declare_code', 'declare_price', 
            'sid_list', 'attr_list', 'think', 'description', 'img_list', 'parameter'
        ];
        $scenarios[self::SCENARIO_UPDATE] = [
            'id', 'cid', 'bid', 'is_sensitive', 'sex', 'cost', 'declare_cname', 
            'declare_ename', 'declare_code', 'declare_price', 'sid_list', 
            'attr_list', 'think', 'description'
        ];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'cid' => 'Cid',
            'bid' => 'Bid',
            'sid_list' => 'Sid List',
            'spu_code' => 'Spu Code',
            'is_sensitive' => 'Is Sensitive',
            'sex' => 'Sex',
            'cost' => 'Cost',
            'description' => 'Description',
            'attr_list' => 'Attr List',
            'think' => 'Think',
            'declare_cname' => 'Declare Cname',
            'declare_ename' => 'Declare Ename',
            'declare_price' => 'Declare Price',
            'declare_code' => 'Declare Code',
            'create_time' => 'Create Time',
            'create_by' => 'Create By',
            'update_time' => 'Update Time',
            'update_by' => 'Update By',
            'is_del' => 'Is Del',
        ];
    }

    /**
     * 产品对应分类oms_category 一对一
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'cid'])->select(['id', 'name']);
    }

    /**
     * 产品对应品牌oms_brand 一对一
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'bid'])->select(['id', 'name']);
    }

    /**
     * 产品对应品牌oms_brand 一对一
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'create_by'])->select(['id', 'username']);
    }

    

    /**
     * 产品对sku 一对多
     */
    public function getSku()
    {
        return $this->hasMany(ProductSku::className(), ['pid' => 'id'])->select(['id', 'sku_code', 'sku_attribute']);
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

    /**
     * 获取产品列表 
     */
    public function getList($fields = '*', $where = [], $limit = 100, $offset = 0)
    {
        $data = self::find()
            ->where($where)
            ->andWhere(['is_del' => 0])
            ->with('category')
            ->with('brand')
            ->select($fields)
            ->limit($limit)
            ->offset($offset)
            ->orderBy('create_time DESC')
            ->asArray()->all();

        foreach ($data as &$v) {
            $v['category_name'] = $v['category']['name'] ?? '';
            $v['brand_name'] = $v['brand']['name'] ?? '';
            $v['img_list'] = json_decode($v['img_list'], true);
            $v['create_time'] = date('Y-m-d', $v['create_time']);
            $v['open_level'] = self::LEVEL_LIST[$v['open_level']];
            unset($v['category'], $v['brand']);
        }

        return $data;
    }

    public static function getOne($fields, $where)
    {
        $data = self::find()
            ->where($where)
            ->select($fields)
            ->with('category')
            ->with('brand')
            ->with('user')
            ->asArray()->one();

        return $data;
    }

}
