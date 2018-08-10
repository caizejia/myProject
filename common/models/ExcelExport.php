<?php
/**
 * Created by PhpStorm.
 * User: 秦洋
 * Date: 2018/3/1
 * Time: 10:54
 */
namespace common\models;
use PHPExcel;
use Yii;


class ExcelExport extends \yii\base\Model{

    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    public function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }

        return $obj;
    }

}