<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\Supplier;
use api\components\Error;

/**
 * 产品供应商.
 */
class SupplierController extends BaseController
{
    /**
     * @fun 产品供应商列表
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
        // 查询供应商数据
        $query = Supplier::find()
            ->where(['is_del' => '0']);
        $data = $query->select(['id', 'name', 'area', 'platform', 'purchase_price',
            'minimum_quantity', 'link_name', 'link_phone', 'email', 'create_time', ])
            ->limit($length)
            ->offset($offset)
            ->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d', $v['create_time']);
        }
        $count = $query->count();
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = $count;

        return $this->jsonReturn($res);
    }

    /**
     * @fun 新增产品供应商
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionCreate()
    {
        // 实例化
        $sModel = new Supplier();
        $sModel->setScenario('create');
        // 数据验证
        if ($sModel->load(Yii::$app->request->post(), '') && $sModel->validate()) {
            // 保存数据
            $res = $sModel->save(false);
            if ($res) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(500);
            }
        } else {
            // 参数验证失败
            $error = $sModel->errors;
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
     * @fun 更新产品供应商信息
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $sModel = Supplier::find()->
            andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();
        $sModel->setScenario('update');
        $a = Yii::$app->request->bodyParams;
        // bodyParams 获取PUT参数
        if ($sModel->load(Yii::$app->request->bodyParams, '') && $sModel->validate()) {
            $res = $sModel->save(false);
            if ($res) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(500);
            }
        } else {
            // 参数验证失败
            $error = $sModel->errors;
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
     * @fun 软删除产品供应商
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $sModel = Supplier::find()->
            andWhere(['id' => Yii::$app->request->get('id')])->one();
        $sModel->is_del = 1;
        // 不需要数据验证
        $res = $sModel->save(false);
        if ($res) {
            return $this->jsonReturn();
        } else {
            return Error::errorJson(500);
        }
    }
}
