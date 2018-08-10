<?php

namespace api\modules\v1\oms\controllers;

use api\models\AdWebsite;
use Yii;
use api\components\Error;
use api\models\Translation;

/**
 * 产品站点控制器.
 */
class AdWebsiteController extends BaseController
{
    /**
     * @fun 产品站点
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author JW
     * @date 2018/06/01
     */
    public function actionIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询供应商数据
        $query = AdWebsite::find()
            ->where(['is_del' => '0']);
        $data = $query
            ->limit($length)
            ->offset($offset)
            ->asArray()->all();
        $count = $query->count();
        foreach ($data as &$v) {
            $v['create_name'] = Translation::UserName($v['create_by']) ?? '';
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
        }
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = $count;

        return $this->jsonReturn($res);
    }

    /**
     * @fun 新增站点
     *
     * @author JW
     * @date 2018/06/01
     */
    public function actionCreate()
    {
        // 实例化
        $sModel = new AdWebsite();
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
     * @fun 更新站点
     *
     * @param id 产品分类id
     *
     * @author JW
     * @date 2018/06/01
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $sModel = AdWebsite::find()->
        andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();

        $a = Yii::$app->request->bodyParams;
        // bodyParams 获取PUT参数
        if ($sModel->load(Yii::$app->request->bodyParams, '') && $sModel->validate()) {
            $sModel->create_time = time();
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
     * @fun 软删除站点
     *
     * @param id 产品分类id
     *
     * @author JW
     * @date 2018/06/01
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $sModel = AdWebsite::find()->
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
