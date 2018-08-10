<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "oms_translation".
 *
 * @property int $id 翻译表id
 * @property string $title 标题名称
 * @property int $uid 需求人
 * @property string $t_language 需求翻译语言
 * @property int $level 紧急程度（0低 1中 2高）
 * @property string $remark 需求备注
 * @property int $fix_uid 指派人员id
 * @property int $status 翻译状态（0提交任务 1翻译完成 2审核翻译 3不通过再翻译 4完成翻译
 * @property string $content 翻译文案内容
 * @property string $ext_url 翻译图片
 * @property int $create_time 创建时间
 * @property int $update_time 最后更新时间
 * @property int $update_by 最后更新人
 * @property int $final_time 任务完成时间
 * @property int $is_del 是否删除（0否 1是）
 */
class Translation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oms_translation';
    }

    public $levelstatus = [
        '处理中'   => 0,
        '重新派送' => 4,
        '取消订单' => 3,
        '拒签'    => 1,
        '签收'    => 2
    ];


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'level', 't_language', 'title'], 'required', 'message' => "请输入{attribute}", 'on' => ['create', 'update']],
            [['uid', 'level', 'status', 'create_time', 'update_time', 'update_by', 'final_time', 'is_del','translate_time','fix_time','design_uid','count'], 'integer'],
            [['title'], 'string', 'max' => 64],
            [['t_language'], 'string', 'max' => 2],
            [['remark','unpass_reason'], 'string', 'max' => 128],
            [['content_url','type', 'ext_url', 'fix_uid','design_url'], 'safe'],
        ];
    }

    /**
     * 自定义两个必须有一个必填
     */
    public function chooseOne($attr, $params)
    {
        if ($this->content == '' && $this->ext_url == '') {
            $this->addError($attr, "翻译内容或者翻译附件未上传");
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'uid' => 'Uid',
            't_language' => 'T Language',
            'level' => 'Level',
            'remark' => 'Remark',
            'fix_uid' => 'Fix Uid',
            'status' => 'Status',
            'content' => 'Content',
            'translate_url' => 'translate_url',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'update_by' => 'Update By',
            'final_time' => 'Final Time',
            'is_del' => 'Is Del',
            'translate_time'=>'translate_time'
        ];
    }

    /**
     * 字段应用场景
     */ 
    public function scenarios()
    {

        return [
            'create' => ['level', 'fix_uid', 'title', 't_language', 'translate_url', 'uid', 'content_url', 'remark','is_del','status','design_url','unpass_reason','count','design_status'],
            'update' => ['id', 'level', 'fix_uid', 'title', 't_language', 'translate_url', 'uid', 'content_url', 'remark', 'final_time', 'status','design_url','unpass_reason','count','design_status'],
            'fix' => ['id', 'fix_uid', 'title', 't_language', 'ext_url', 'uid', 'content_url', 'remark', 'final_time', 'status','fix_time','design_url','unpass_reason','count','design_status'],
        ];
    }

    /**
     * 关联用户表
     */ 
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid'])->select(['id', 'username']);
    }


    public static function UserName($id)
    {
        $UserName = User::findOne($id);
        return $UserName['username'];
    }

    public static function status($status)
    {
        if ($status ==0){
            return '提交任务';
        }elseif ($status ==1){
            return '翻译完成';
        }elseif ($status ==2){
            return '审核翻译';
        }elseif ($status ==3){
            return '不通过再翻译';
        }elseif ($status ==4){
            return '完成翻译';
        }elseif ($status ==5){
            return '翻译进行中';
        }
    }

    /**
     * 关联用户表 设计师
     */ 
    public function getDesigner()
    {
        return $this->hasOne(User::className(), ['id' => 'fix_uid'])->select(['id', 'username']);
    }

}
