<?php

namespace api\models;

use Yii;
use api\components\Image\AipImageSearch;
/**
 * This is the model class for table "oms_product_center".
 *
 * @property string $id
 * @property string $classify 分类 
 * @property string $product_type 类别  普货 敏感货
 * @property int $open 可见  0没设置   1组内可见  2 所有人可见        
 * @property string $sex 性别  男 女 通用
 * @property string $name 产品名称
 * @property string $spu spu
 * @property string $image 主图
 * @property string $website 站点
 * @property int $user_id 添加人员
 * @property int $repeat_status 图片审核0未通过 1通过
 * @property string $remark 备注
 */
class ProductCenter extends \yii\db\ActiveRecord
{

    const APP_ID = '11221265';
    const API_KEY = 'xDMDjGBZ89t6j8TuEfbe8cul';
    const SECRET_KEY = 'qLcd62gxghgMGie9u8hZbtiGNLaHK0Vb';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_product_center';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['classify', 'product_type', 'sex', 'name', 'spu'], 'required'],
            [['open', 'user_id','repeat_status'], 'integer'],
            [['classify', 'product_type'], 'string', 'max' => 32],
            [['sex'], 'string', 'max' => 16],
            [['name', 'image', 'remark'], 'string', 'max' => 255],
            [['spu'], 'string', 'max' => 50],
            [['image'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'classify' => 'Classify',
            'product_type' => 'Product Type',
            'open' => 'Open',
            'sex' => 'Sex',
            'name' => 'Name',
            'spu' => 'Spu',
            'image' => 'Image',
            'website' => 'Website',
            'user_id' => 'User ID',
            'repeat_status' => 'Repeat Status',
            'remark' => 'Remark',
        ];
    }


    /**
     * 生成sku
     */
    public static function sku($model, $ci)
    {

        //sku头部
        $classify = $model['classify'];
        //序号
        $id = '00001';

        //货物类型
        $product_type = $model['product_type'];
        if ($product_type == '普货') {
            $product_type = 'P';
        } else {
            $product_type = 'M';
        }
        //性别
        $sex = $model['sex'];
        if (empty($sex)) {
            $sex = '0';
        }

        //查询product是否有000001.
        $spu_like = $classify.'%';
        $idDate = self::find()->select(['spu', 'classify'])->where("spu like '{$spu_like}'")->asArray()->all();

        if ($idDate) {
            foreach ($idDate as $v) {

                $strsize[] = intval(mb_substr($v['spu'], 1, 5,'utf-8'));
            }

            if ($ci == 1) {
                $max = max($strsize);
                $max2 = $max + 1;
            } else {
                $max = max($strsize);
                $max2 = $max > 0 ? $max+1 : 1;
            }
            $strdisp = sprintf("%'.05d", $max2);
            $skus = $classify . $strdisp . $product_type . $sex;
            return $skus;
        } else {
            $skus = $classify . $id . $product_type . $sex;
            return $skus;
        }
    }
    public static function SearchPictureDetection($name,$id,$src){
        $client = new AipImageSearch(self::APP_ID, self::API_KEY, self::SECRET_KEY);
        $options = array();
        $image_info = [];
        $image_info['name'] = $name;
        $image_info['id'] = $id; //上传的唯一id ,可以对应商品的id
        $options["brief"] = json_encode($image_info);
        // 调用相同图检索—检索
        $image = file_get_contents($src);  //小范围外观改变测试
        $ret = $client->sameHqSearch($image);
        return $ret;

    }

    public static function AddPictureDetection($name,$id,$src){
        $client = new AipImageSearch(self::APP_ID, self::API_KEY, self::SECRET_KEY);
        $options = array();
        $image_info = [];
        $image_info['name'] = $name;
        $image_info['id'] = $id; //上传的唯一id ,可以对应商品的id
        $options["brief"] = json_encode($image_info);
        // 调用相同图检索—入库
        $image = file_get_contents($src);
        $ret = $client->sameHqAdd($image, $options);
        return $ret;

    }




}
