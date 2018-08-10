<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wms_uploads".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $purpose
 * @property string $file_name
 * @property string $update_time
 * @property integer $is_use
 * @property string $use_time
 * @property integer $use_result
 */
class WmsUploads extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wms_uploads';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'is_use', 'use_result'], 'integer'],
            [['file_name', 'update_time'], 'required'],
            [['update_time', 'use_time'], 'safe'],
            [['purpose', 'file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'purpose' => 'Purpose',
            'file_name' => 'File Name',
            'update_time' => 'Update Time',
            'is_use' => 'Is Use',
            'use_time' => 'Use Time',
            'use_result' => 'Use Result',
        ];
    }
}
