<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_product_check".
 *
 * @property int $id 产品检查重复表id
 * @property string $name 产品名称
 * @property string $img 产品图片
 * @property int $status 检查状态（0通过 1需人工排重 2重复）
 * @property string $remark 检查备注
 * @property int $create_time 创建时间
 * @property int $create_by 创建人
 * @property int $is_del 是否删除（0否 1是）
 */
class ProductCheck extends \yii\db\ActiveRecord
{
    const CHECK_LIST = [
        '通过',
        '人工审核',
        '未通过',
        '申请可上架',
        '申请不可上架',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_product_check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'img'], 'required', 'message' => "请输入{attribute}", 'on' => ['create']],
            [['status', 'create_time', 'create_by', 'is_del'], 'integer'],
            [['name', 'img', 'remark'], 'string', 'max' => 128],
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
        $scenarios[self::SCENARIO_CREATE] = ['name', 'img', 'status'];
        $scenarios[self::SCENARIO_UPDATE] = ['id', 'status', 'update_time', 'update_by', 'is_del'];

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
            'img' => 'Img',
            'status' => 'Status',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
            'create_by' => 'Create By',
            'is_del' => 'Is Del',
        ];
    }

    /**
     * 检查图片是否已存在
     */
    public function checkImgExists($img)
    {
        return $this->find()->where([
            'is_del' => 0,
            'img' => $img,
        ])->one();
    }

    /**
     * 获取数据数量
     */
    public function getCount($where)
    {
        $count = self::find()->where($where)->count();

        return $count;
    }

    /**
     * 获取数据列表
     */
    public function getList($fields = '*', $where = [], $limit = 100, $offset = 0)
    {
        $data = self::find()
            ->where($where)
            ->andWhere(['is_del' => 0])
            ->select($fields)
            ->limit($limit)
            ->offset($offset)
            ->orderBy('create_time DESC')
            ->asArray()->all();

        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['status'] = self::CHECK_LIST[$v['status']];
        }

        return $data;
    }

    public static function getOne($fields, $where)
    {
        $data = self::find()
            ->where($where)
            ->select($fields)
            ->asArray()->one();

        return $data;
    }

}
