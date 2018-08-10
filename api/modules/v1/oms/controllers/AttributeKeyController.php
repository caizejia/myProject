<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\AttributeKey;
use api\components\Error;

/**
 * 产品评论.
 */
class AttributeKeyController extends BaseController
{
    /**
     * @fun 产品评论列表
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionIndex()
    {
        $offset = Yii::$app->request->get('offset');
        //$length = Yii::$app->request->get('length', 10);
        $length = 10;
        if ($offset > 0 && gettype($offset) == 'integer') {
            $offset = ($offset - 1) * 10;
        } else {
            $offset = 0;
        }

        // 查询属性数据
        $query = AttributeKey::find();
        $data = $query->select(['id', 'language', 'name'])
            ->limit($length)
            ->offset($offset)
            ->asArray()->all();
        $count = $query->count();

        $res = [];
        $res['list'] = $data;
        $res['page_total'] = ceil($count / $length);

        return $this->jsonReturn($res);
    }

    /**
     * @fun 新增属性
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionCreate()
    {
        // 实例化
        $aModel = new AttributeKey();
        // 数据验证
        if (Yii::$app->request->post()) {
            $postData = [
                            'CN' => Yii::$app->request->post('CN'),
                            'EN' => Yii::$app->request->post('EN'),
                        ];

            $asc = $aModel->find()->select('aid')->orderBy('aid DESC')->one();
            $last = $asc->aid + 1;
            $data = $aModel->find()->asArray()->all();
            $arr = [];
            $time = time();
            foreach ($postData as $k1 => $v1) {
                foreach ($data as $v2) {
                    if ($v1 == $v2['name']) {
                        return Error::errorJson(403, '不能输入重复属性');
                    }
                }
                $arr[] = "('{$last}','{$k1}','{$v1}','{$time}')";
            }

            $arr = implode(',', $arr);
            $res = Yii::$app->db->createCommand("INSERT INTO oms_attribute_key (aid,language,name,create_time) VALUES $arr")->execute();

            if ($res) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(500);
            }
        } else {
            // 参数验证失败
            if (empty($error)) {
                $msg = '没有传参，无法验证数据';
            } else {
                $msg = '';
            }

            return Error::errorJson(403, $msg);
        }
    }

    /**
     * @fun 更新产品评论信息
     *
     * @param id 评论id
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $aModel = AttributeKey::find()->andWhere(['id' => Yii::$app->request->get('id')])->one();
        $aModel->setScenario('update');
        // bodyParams 获取PUT参数
        if ($aModel->load(Yii::$app->request->bodyParams, '') && $aModel->validate()) {
            $params = Yii::$app->request->bodyParams;
            $chong = AttributeKey::find()->where(['name' => $params['name']])->one();
            if ($chong) {
                return Error::errorJson(403, '不能输入重复属性');
            }

            $res = $aModel->save(false);
            if ($res) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(500);
            }
        } else {
            // 参数验证失败
            $error = $pcModel->errors;
            if (empty($error)) {
                $msg = '没有传参，无法验证数据';
            } else {
                $msg = '';
            }
            foreach ($error as $key) {
                $msg .= $key[0].' ';
            }

            return Error::errorJson(403, $msg);
        }
    }

    // /**
    //  * @fun 软删除产品评论
    //  *
    //  * @param id 评论id
    //  *
    //  * @author FHH
    //  * @date 2018/06/04
    //  */
     public function actionDelete()
     {
         // url上的id使用get方式获取
         $aModel = AttributeKey::find()->andWhere(['id' => Yii::$app->request->get('id')])->one();
         $aModel->is_del = 1;
         // 不需要数据验证
         $res = $aModel->save(false);
         if ($res) {
             return $this->jsonReturn();
         } else {
             return Error::errorJson(500);
         }
     }
}
