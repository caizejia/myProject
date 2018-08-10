<?php

namespace api\modules\v1\oms\controllers;

use api\models\Customer;
use Yii;
use api\components\Error;

class CustomerController extends BaseController
{
    public function actionIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询供应商数据
        $data = Customer::find()
            ->where(['is_del' => '0'])
            ->limit($length)
            ->offset($offset)
            ->asArray()->all();

        return $this->jsonReturn($data);
    }

    public function actionCreate()
    {
        // 实例化
        $sModel = new Customer();

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
     * @fun 更新客户id
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $sModel = Customer::find()->
        andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();
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
     * @fun 软删除
     *
     * @param id 客户id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $sModel = Customer::find()->
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
