<?php

namespace common\models;

use Yii;
use common\models\WmsProductDetails;
/**
 * This is the model class for table "wms_product".
 *
 * @property string $id
 * @property string $cid
 * @property string $name
 * @property string $spu
 * @property string $old_sku
 * @property string $thumb
 * @property integer $view_level
 * @property string $attribute_list
 * @property string $price
 * @property string $virtual_price
 * @property string $activity_id
 * @property string $city_code
 * @property string $web_other
 * @property string $bounce_rate
 * @property string $pv
 * @property string $remark
 * @property string $ads_user
 * @property string $has_gift
 * @property integer $is_ads
 * @property integer $is_on_sale
 * @property string $ads_time
 * @property string $create_time
 * @property string $update_time
 * @property string $user_id
 * @property string $update_by
 * @property integer $is_del
 * @property string $generate_count
 */
class WmsProduct extends \yii\db\ActiveRecord
{
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
        $scenarios[self::SCENARIO_CREATE] = ['name', 'keyword', 'cid', 'bid', 'is_sensitive', 'sex', 'cost', 'declare_cname', 'declare_ename', 'declare_code', 'declare_price', 'sid_list', 'attr_list', 'think', 'description', 'img_list', 'parameter'];
        $scenarios[self::SCENARIO_UPDATE] = ['id', 'cid', 'bid', 'is_sensitive', 'sex', 'cost', 'declare_cname', 'declare_ename', 'declare_code', 'declare_price', 'sid_list', 'attr_list', 'think', 'description'];

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
     * 产品对sku 一对多
     */
    public function getSku()
    {
        return $this->hasMany(ProductSku::className(), ['pid' => 'id'])->select(['id', 'sku_code', 'sku_attribute']);
    }

    //获得产品详细信息 from pid
    public static function getInfo($pid){
        $cache = Yii::$app->cache;
        $cache_name = 'product:'.$pid;
        $data = $cache->get($cache_name);
        if ($data === false) {
            //这里我们可以操作数据库获取数据，然后通过$cache->set方法进行缓存
            $cacheData = self::findOne($pid);
            //set方法的第一个参数是我们的数据对应的key值，方便我们获取到
            //第二个参数即是我们要缓存的数据
            //第三个参数是缓存时间，如果是0，意味着永久缓存。默认是0
            $cache->set($cache_name, $cacheData, 3600*1);
            $data = $cache->get($cache_name);
        }
        return $data;
    }

    //获得产品详细信息 from sku
    public static function getSkuInfo($sku){
        $sku_info = WmsProductDetails::getGoodsId($sku);
        $pid = $sku_info['pid'];
        $data = self::getInfo($pid);
        return $data;
    }

}
