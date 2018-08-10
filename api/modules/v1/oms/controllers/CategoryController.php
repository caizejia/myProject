<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\Category;
use api\components\Error;
use api\components\Funs;

/**
 * 产品分类控制器.
 */
class CategoryController extends BaseController
{
    /**
     * @fun 产品分类列表
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
        // 查询分类数据
        $cModel = new Category();
        $data = $cModel->getList($length, $offset);
        $pagination = Funs::get_page_info($cModel, '', $offset, $length);

        return $this->jsonReturn($data, '', $pagination);
    }

    public function actionInitData()
    {
        $cModel = new Category();
        $cateList = $cModel->categoryList();
        $data['cate_list'] = $cateList;
        return $this->jsonReturn($data);
    }

    /**
     * @fun 新增产品分类
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionCreate()
    {
        // 实例化
        $cModel = new Category();
        $cModel->setScenario('create');
        $postData = Yii::$app->request->post();
        // 数据验证
        if ($cModel->load($postData, '') && $cModel->validate()) {
            // 保存数据
            $res = $cModel->save(false);
            if ($res) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(500);
            }
        } else {
            // 参数验证失败
            $error = $cModel->errors;
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
     * @fun 更新产品分类信息
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $cModel = Category::find()->
            andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();
        $cModel->setScenario('update');
        // bodyParams 获取PUT参数
        if ($cModel->load(Yii::$app->request->bodyParams, '') && $cModel->validate()) {
            $res = $cModel->save(false);
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
     * @fun 软删除产品分类
     *
     * @param id 产品分类id
     *
     * @author YXH
     * @date 2018/05/29
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $cModel = Category::find()->
            andWhere(['id' => Yii::$app->request->get('id')])->one();
        $cModel->is_del = 1;
        // 不需要数据验证
        $res = $cModel->save(false);
        if ($res) {
            return $this->jsonReturn();
        } else {
            return Error::errorJson(500);
        }
    }
}
