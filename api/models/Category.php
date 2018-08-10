<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_category".
 *
 * @property int $id 分类表id
 * @property int $parent_id 父id（默认为0）
 * @property string $name 分类名称
 * @property int $sort 排序
 * @property int $create_time 创建时间
 * @property int $is_del 是否软删除（0否 1是）
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oms_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'name', 'sort'], 'required', 'message' => "请输入{attribute}", 'on' => ['create', 'update']],
            [['parent_id', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['sort'], 'string', 'max' => 3],
            [['is_del'], 'string', 'max' => 1],
        ];
    }

    /**
     * 字段应用场景
     */ 
    public function scenarios()
    {
        return [
            'create' => ['parent_id', 'name', 'sort'],
            'update' => ['parent_id', 'name', 'sort'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'is_del' => 'Is Del',
        ];
    }

    /**
     * 自关联查询父id名称
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id'])
            ->from(self::tableName() . ' AS parent');
    }

    /**
     * 自关联查询子分类
     */
    public function getChild()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id'])
            ->from(self::tableName() . ' AS child');
    }

    /**
     * 关联品牌表 一对多
     */
    public function getBrands()
    {
        return $this->hasMany(Brand::className(), ['id' => 'cid']);
    }

    /**
     * 获取产品分类列表
     */
    public function getList($limit = 20, $offset = 1)
    {
        $data = self::find()
            ->from(self::tableName().' AS C')
            ->where(['C.is_del' => '0'])
            ->joinWith(['parent'])
            ->select(['C.id', 'C.parent_id', 'C.name', 'C.create_time'])
            ->limit($limit)
            ->offset($offset)
            ->orderBy('C.sort DESC')
            ->asArray()->all();

        foreach ($data as &$v) {
            $v['parent_name'] = $v['parent']['name'] ?? '';
            $v['create_time'] = date('Y-m-d', $v['create_time']);
            unset($v['parent']);
        }

        return $data;
    }

    /**
     * 获得树状结构的数组 cascader vue选择列表
     */
    public static function categoryList()
    {
        $data = self::find()->select('id, name, parent_id')
            ->where(['is_del' => 0])
            ->asArray()->orderBy('sort DESC')->all();

        return $data;
        //$tree = [];
        //$this->getTree($data, $tree);
        //return $tree;
    }

    /**
     * 获取分类数组结构
     */
    function getTree(&$list,&$tree,$pid=0){
        foreach($list as $key=>$value){
            if($pid == $value['parent_id']){
                $tree[$value['value']] = $value;
                unset($list[$key]);
                $this->getTree($list,$tree[$value['value']]['children'],$value['value']);
            }
        }
    }

    public static function getSelectedOption($id, &$selectArr = [])
    {
        $data = self::findOne($id);
        array_unshift($selectArr, (string)$id);
        if ($data['parent_id'] != 0) {
            self::getSelectedOption($data['parent_id'], $selectArr);
        } else {
            return $selectArr;
        }
    }

}
