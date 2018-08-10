<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_channel_services".
 *
 * @property string $id
 * @property string $country 国家代码
 * @property string $post_code
 * @property string $channel 服务商
 */
class ChannelServices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_channel_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country', 'post_code', 'channel'], 'required'],
            [['country'], 'string', 'max' => 2],
            [['post_code', 'channel'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country' => 'Country',
            'post_code' => 'Post Code',
            'channel' => 'Channel',
        ];
    }

    /**
     * 判断订单是否在派送区域
     * @param $country
     * @param $post_code
     * @return bool
     */
    public function checkPost($country, $post_code){
        $data = $this->find()->andWhere(['country' => $country])->andWhere(['post_code' => $post_code])->one();
        if($data)
        {
            return true;
        }else{
            return false;
        }
    }


    /**
     * 通过邮编获取国家的派送公司
     * @param $country
     * @param $post_code
     * @return bool|string
     */
    public function getChannelByPostCode($country, $post_code)
    {
        //泰国优先使用NJV，NJV配送不到的地方才先用KERRY
        if($country == 'TH')
        {
            $data = $this->find()->where(['post_code' => $post_code, 'country' => $country, 'channel' => 'KERRY'])->one();
            if(!$data)
            {
                $data = $this->find()->where(['post_code' => $post_code, 'country' => $country, 'channel' => 'NJV'])->one();
            }
        }elseif($country == 'MY'){
            $data = $this->find()->where(['post_code' => $post_code, 'country' => $country, 'channel' => 'DHL'])->one();
            //马来优先发DHL
//            $data = $this->find()->where(['post_code' => $post_code, 'country' => $country, 'channel' => 'GDEX'])->one();
            if(!$data)
            {
                $data = $this->find()->where(['post_code' => $post_code, 'country' => $country, 'channel' => 'GDEX'])->one();
            }
        }else{
            $data = $this->find()->where(['post_code' => $post_code, 'country' => $country])->one();
        }

        if($data)
        {
            return $data->channel;
        }else{
            return false;
        }
    }
}
