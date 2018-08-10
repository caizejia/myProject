<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\Brand;
use api\components\Error;

/**
 * 产品品牌.
 */
class BrandController extends BaseController
{
    /**
     * @fun 产品品牌列表
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author YXH 
     * @date 2018/05/28
     */
    public function actionIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询品牌数据
        $query = Brand::find()
            ->from(Brand::tableName().' AS B')
            ->where(['B.is_del' => '0'])
            ->with('category');
        $data = $query->select(['B.id', 'B.cid', 'B.name', 'B.logo', 'B.create_time'])
            ->limit($length)
            ->offset($offset)
            ->asArray()->all();
        $count = $query->count();
        foreach ($data as &$v) {
            $v['cate_name'] = $v['category']['name'] ?? '';
            $v['create_time'] = date('Y-m-d', $v['create_time']);
            unset($v['category']);
        }
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = $count;

        return $this->jsonReturn($res);
    }

    /**
     * @fun 新增产品品牌
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionCreate()
    {
        // 实例化
        $bModel = new Brand();
        $bModel->setScenario('create');
        $postData = Yii::$app->request->post();
        // 数据验证
        if ($bModel->load($postData, '') && $bModel->validate()) {
            // 保存数据
            $res = $bModel->save(false);
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

    /**
     * @fun 更新产品品牌信息
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $bModel = Brand::find()->
            andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();
        $bModel->setScenario('update');
        // bodyParams 获取PUT参数
        if ($bModel->load(Yii::$app->request->bodyParams, '') && $bModel->validate()) {
            $res = $bModel->save(false);
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

    /**
     * @fun 软删除产品品牌
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $bModel = Brand::find()->
            andWhere(['id' => Yii::$app->request->get('id')])->one();
        $bModel->is_del = 1;
        // 不需要数据验证
        $res = $bModel->save(false);
        if ($res) {
            return $this->jsonReturn();
        } else {
            return Error::errorJson(500);
        }
    }
}
