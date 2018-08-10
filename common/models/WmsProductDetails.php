<?php

namespace common\models;

use Yii;
use api\components\Funs;

/**
 * This is the model class for table "oms_product_sku".
 *
 * @property int $id sku表id
 * @property int $pid 产品id
 * @property string $sku_code sku货码
 * @property string $sku_attribute 属性值id列表
 * @property int $product_stock 库存数量
 * @property string $price 价格
 * @property string $image 单图选择时展示
 * @property int $create_time 创建时间
 * @property int $update_time 更新时间
 * @property int $create_by 创建人
 * @property int $update_by 更新人
 * @property int $is_del 是否删除（0否 1是）
 */
class WmsProductDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_product_sku';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid'], 'required'],
            [['pid', 'create_time', 'update_time', 'create_by', 'update_by', 'is_del'], 'integer'],
            [['sku_code'], 'string', 'max' => 32],
            [['sku_attribute'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'sku_code' => 'Sku Code',
            'sku_attribute' => 'Sku Attribute',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'create_by' => 'Create By',
            'update_by' => 'Update By',
            'is_del' => 'Is Del',
        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'],
            'create' => ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'],
            'update' => ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'],
        ];
    }

    
    /**
     * 处理属性组合集合 批量入库
     * @author YXH 
     * @date 2018/06/25
     */
    public function add($psData)
    {
        $field = ['pid', 'sku_code', 'sku_attribute', 'create_time', 'create_by'];
        $time = time();
        $addData = $arr = [];
        $list = $psData['attr_list'];
        $attrData = Funs::get_arr_set($list);
        foreach ($attrData as $v) {
            $temp = [];
            foreach ($v as $node) {
                $temp[$node['attr']] = $node['value'];
            }
            $addData[] = [
                $psData['pid'],
                'sku000',
                json_encode($temp, JSON_UNESCAPED_UNICODE),
                $time,
                $psData['uid'],
            ];
        }
        // 一次批量插入
        $tableName = self::tableName();
        $successNum = Yii::$app->db->createCommand()->batchInsert($tableName,$field,$addData)->execute();

        return $successNum;
    }

    /**
     * 通过id获得sku
     * @param $goods_id
     * @return mixed
     */
    public static function getSku($goods_id){
        $cache = Yii::$app->cache;
        $cache_name = 'goods:'.$goods_id;
        $data = $cache->get($cache_name);
        if ($data === false) {
            //这里我们可以操作数据库获取数据，然后通过$cache->set方法进行缓存
            $cacheData = self::findOne($goods_id);
            //set方法的第一个参数是我们的数据对应的key值，方便我们获取到
            //第二个参数即是我们要缓存的数据
            //第三个参数是缓存时间，如果是0，意味着永久缓存。默认是0
            $cache->set($cache_name, $cacheData, 3600*24);
            $data = $cache->get($cache_name);
        }
        return $data;
    }

    //获得产品详细信息  
    public static function getFullInfo($goods_id){ 

        $cache = Yii::$app->cache;
        $cache->flush(); //测试环境，清空缓存
        $cache_name = 'full_goods:'.$goods_id;
        $data = $cache->get($cache_name);
        if ($data === false) {

            $sub_inventory_info = \Yii::$app->db->createCommand("select * from oms_product_sku as A left join oms_product  as B on A.pid = B.id where  A.id = {$goods_id}")->queryOne();
            $sub_inventory_info['image'] = json_decode($sub_inventory_info['img_list'])[0];
//            $sub_inventory_info['sku_attribute'] = json_decode($sub_inventory_info['sku_attribute'],true);

            $cache->set($cache_name, $sub_inventory_info, 3600*1);
            $data = $cache->get($cache_name);
        }
        return $data;
    }

    /**
     * 通过sku获得goods_id
     * @param $goods_id
     * @return mixed
     */
    public static function getGoodsId($sku){
        $cache = Yii::$app->cache;
//        echo $sku;
        $data = $cache->get($sku);
//        $cache->delete($sku);die;
//        $cache->flush();
//        2.读取多个缓存
//          $cache->mget(['nameone','nametwo']);
//          判断缓存是否存在
//          $cache->exists('name');
        if ($data === false) {
            //这里我们可以操作数据库获取数据，然后通过$cache->set方法进行缓存
            $cacheData = self::find()->andWhere(['=','sku_code',$sku])->one();
            //set方法的第一个参数是我们的数据对应的key值，方便我们获取到
            //第二个参数即是我们要缓存的数据
            //第三个参数是缓存时间，如果是0，意味着永久缓存。默认是0
            $cache->set($sku, $cacheData, 3600*24);
            $data = $cache->get($sku);
        }
        return $data;
    }
    
}
