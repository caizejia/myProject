<?php

namespace api\modules\v1\oms\controllers;

use api\components\Error;
use api\models\SalesCity;
use Yii;

class SalesCityController extends BaseController
{
    /**
     * @fun 城市
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
        $page = Yii::$app->request->get('page');//页码
        $pagesize = Yii::$app->request->get('pagesize');//
        // 查询供应商数据
        $query = SalesCity::find()
            ->where(['is_del' => '0']);
        $data = $query
            ->limit($pagesize)
            ->offset(($page - 1) * $pagesize)
            ->orderBy('id DESC')
            ->asArray()->all();
        $count = $query->count();
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = $count;

        return $this->jsonReturn($res);
    }

    /**
     * @fun 新增城市
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionCreate()
    {
        // 实例化
        $sModel = new SalesCity();
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
     * @fun 更新城市
     *
     * @param id 城市id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $sModel = SalesCity::find()->
        andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();

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
     * @fun 软删除城市
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $sModel = SalesCity::find()->
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
