<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/1/11
 * Time: 14:57
 */
namespace common\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file','extensions' => 'xls,xlsx',],
            [['purpose'], 'safe'],
        ];
    }
}