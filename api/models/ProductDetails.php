<?php

namespace api\models;

use Yii;
use api\components\Error;
/**
 * This is the model class for table "oms_product_details".
 *
 * @property string $id
 * @property string $spu spu
 * @property string $color 颜色
 * @property string $size 尺寸
 * @property string $sku sku
 * @property int $combination 是否有组合产品  0 没有 1 有
 * @property string $image 供应商
 * @property int $user_id 添加人员
 */
class ProductDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_product_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spu', 'sku'], 'required'],
            [['user_id'], 'integer'],
            [['spu', 'color', 'sku'], 'string', 'max' => 50],
            [['size', 'image'], 'string', 'max' => 255],
            [['combination'], 'string', 'max' => 3],
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


    public static function skuCreate($request,$size)
    {
        $spu = $request['ProductDetails']['spu'];
        $color = $request['ProductDetails']['color'];
        if($sku = Yii::$app->db->createCommand("select id,sku from oms_product_details where spu = '{$spu}' and color = '{$color}' and size = '{$size}'")->queryOne()) {
             Error::errorJson(400,'生成错误,已存在!');
        }else{
            if ($sku = Yii::$app->db->createCommand("select sku from oms_product_details where spu = '{$spu}' and color = '{$color}'")->queryOne()) {
                $sku = $sku['sku'];
                $sku_color = mb_substr($sku,8,2,'utf-8');
                if($sku_size = Yii::$app->db->createCommand("select sku from oms_product_details where spu = '{$spu}' and size = '{$size}'")->queryOne()){
                    $sku_size = $sku_size['sku'];
                    $sku_size = mb_substr($sku_size,10,3,'utf-8');
                    return $spu.$sku_color.$sku_size;
                }else{
                    $sku = Yii::$app->db->createCommand("select sku from oms_product_details where spu = '{$spu}'")->queryAll();
                    $sku_size = [];
                    foreach ($sku as $v){
                        $sku_size[] = mb_substr($v['sku'],10,3,'utf-8');
                    }
                    $sku_size = max($sku_size)+1;
                    $check_s = [];
                    for($i=1;$i<301;$i++){
                        $check_s[$i+300] = $i;
                    }

                    $check = [
                        '',
                        's',
                        'm',
                        'l',
                        'xl',
                        '2xl',
                        '3xl',
                        '4xl',
                        '5xl',
                        '6xl',
                        '7xl',
                        '8xl',
                        '9xl',
                        '10xl',
                        'S',
                        'M',
                        'L',
                        'XL',
                        '2XL',
                        '3XL',
                        '4XL',
                        '5XL',
                        '6XL',
                        '7XL',
                        '8XL',
                        '9XL',
                        '10XL',
                    ];
                    $check = array_merge($check,$check_s);
                    if(in_array($size,$check) || preg_match('/^\d{2}[a-zA-z]{1}$/',$size)){
                        if(empty($size)){
                            $sku_size = 0;
                        }else{
                            $sku_size = $size;
                        }
                        if(in_array($size,['s',
                            'm',
                            'l',
                            'xl',
                        ])){
                            $sku_size = strtoupper($size);
                        }
                        if(in_array($size,['2xl',
                            '3xl',
                            '4xl',
                            '5xl',
                            '6xl',
                            '7xl',
                            '8xl',
                            '9xl',
                            '10xl'])){
                            $sku_size = str_replace('xl','XL',$size);
                        }
                        if(preg_match('/^\d{2}[a-zA-z]{1}$/',$size)){
                            $sku_size = mb_substr($size,0,2,'utf-8').strtoupper(mb_substr($size,2,1,'utf-8'));
                        }
                    }
                    $sku_size = str_pad($sku_size,3,"0",STR_PAD_LEFT);
                    return $spu.$sku_color.$sku_size;
                }
            }else{
                $sku = Yii::$app->db->createCommand("select sku from oms_product_details where spu = '{$spu}'")->queryAll();
                $sku_color = [];
                foreach ($sku as $v){
                    $sku_color[] = mb_substr($v['sku'],8,2,'utf-8');
                }
                $sku_color = max($sku_color)+1;
                $sku_color = str_pad($sku_color,2,"0",STR_PAD_LEFT);
                if($sku_size = Yii::$app->db->createCommand("select sku from oms_product_details where spu = '{$spu}' and size = '{$size}'")->queryOne()){
                    $sku_size = $sku_size['sku'];
                    $sku_size = mb_substr($sku_size,10,3,'utf-8');
                    return $spu.$sku_color.$sku_size;
                }else{
                    $sku = Yii::$app->db->createCommand("select sku from oms_product_details where spu = '{$spu}'")->queryAll();
                    $sku_size = [];
                    foreach ($sku as $v){
                        $sku_size[] = mb_substr($v['sku'],10,3,'utf-8');
                    }
                    $sku_size = max($sku_size)+1;
                    $check_s = [];
                    for($i=1;$i<301;$i++){
                        $check_s[$i+300] = $i;
                    }
                    $check = [
                        '',
                        's',
                        'm',
                        'l',
                        'xl',
                        '2xl',
                        '3xl',
                        '4xl',
                        '5xl',
                        '6xl',
                        '7xl',
                        '8xl',
                        '9xl',
                        '10xl',
                        'S',
                        'M',
                        'L',
                        'XL',
                        '2XL',
                        '3XL',
                        '4XL',
                        '5XL',
                        '6XL',
                        '7XL',
                        '8XL',
                        '9XL',
                        '10XL',
                    ];
                    $check = array_merge($check,$check_s);
                    if(in_array($size,$check) || preg_match('/^\d{2}[a-zA-z]{1}$/',$size)){
                        if(empty($size)){
                            $sku_size = 0;
                        }else{
                            $sku_size = $size;
                        }
                        if(in_array($size,['s',
                            'm',
                            'l',
                            'xl',
                        ])){
                            $sku_size = strtoupper($size);
                        }
                        if(in_array($size,['2xl',
                            '3xl',
                            '4xl',
                            '5xl',
                            '6xl',
                            '7xl',
                            '8xl',
                            '9xl',
                            '10xl'])){
                            $sku_size = str_replace('xl','XL',$size);
                        }
                        if(preg_match('/^\d{2}[a-zA-z]{1}$/',$size)){
                            $sku_size = mb_substr($size,0,2,'utf-8').strtoupper(mb_substr($size,2,1,'utf-8'));
                        }
                    }
                    $sku_size = str_pad($sku_size,3,"0",STR_PAD_LEFT);
                    return $spu.$sku_color.$sku_size;
                }
            }
        }
    }
}
