<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_product_details".
 *
 * @property integer $id
 * @property string $spu
 * @property string $color
 * @property string $size
 * @property string $sku
 * @property integer $combination
 * @property string $image
 * @property integer $user_id
 */
class WmsProductDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_product_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spu', 'sku'], 'required'],
            [['combination', 'user_id'], 'integer'],
            [['spu', 'color', 'sku'], 'string', 'max' => 50],
            [['size', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spu' => 'Spu',
            'color' => 'Color',
            'size' => 'Size',
            'sku' => 'Sku',
            'combination' => 'Combination',
            'image' => 'Image',
            'user_id' => 'User ID',
        ];
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

    //获得产品详细信息  //因为对方的product表，暂时不能完成，TODO
    public static function getFullInfo($goods_id){
        return true;
        $cache = Yii::$app->cache;
        $cache_name = 'full_goods:'.$goods_id;
        $data = $cache->get($cache_name);
        if ($data === false) {

            $sub_inventory_info = \Yii::$app->db->createCommand("select * from wms_product_detail as A left join wms_product_sku as B on A.sub_inventory_id = B.id where goods_id = {$order['pick_goods_id']}")->queryOne(); 

            $cache->set($cache_name, $cacheData, 3600*24);
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
            $cacheData = self::find()->andWhere(['=','sku',$sku])->one();
            //set方法的第一个参数是我们的数据对应的key值，方便我们获取到
            //第二个参数即是我们要缓存的数据
            //第三个参数是缓存时间，如果是0，意味着永久缓存。默认是0
            $cache->set($sku, $cacheData, 3600*24);
            $data = $cache->get($sku);
        }
        return $data;
    }
}
