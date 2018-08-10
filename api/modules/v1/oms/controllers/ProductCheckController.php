<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\ProductCheck;
use api\components\Error;
use api\components\Funs;
use api\components\aip\AipImageSearch;

/**
 * 产品排重检查
 */
class ProductCheckController extends BaseController
{
    // 未通过
    const NO_PASS = 2;

    // 人工审核
    const CHECK = 1;

    // 通过
    const PASS = 0;

    /**
     * @fun 产品排重检查列表
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author YXH 
     * @date 2018/06/05
     */
    public function actionIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询排重检查数据
        $fields = [
            'id', 'name', 'img', 'status', 'create_time'
        ];
        $pcModel = new ProductCheck();
        $data = $pcModel->getList($fields, '', $length, $offset);
        $pagination = Funs::get_page_info($pcModel, '', $offset, $length);
        
        return $this->jsonReturn($data, '', $pagination);
    }

    /**
     * @fun 新增产品排重检查
     *
     * @author YXH
     * @date 2018/06/05
     */
    public function actionCreate()
    {
        // 接收参数
        $postData = Yii::$app->request->post();
        // 实例Model
        $pcModel = new ProductCheck();
        $pcModel->setScenario('create');
        if ($pcModel->load($postData, '') && $pcModel->validate()) {// 参数验证
            // 检查图片是否存在
            if ($pcModel->checkImgExists($postData['img'])) {
                return Error::errorJson(400, '图片已存在，请勿重复提交');
            }
            $pcModel->create_time = time();
            $pcModel->create_by = $this->uid;
            // 百度AIP 图片搜索
            $AIP = new AipImageSearch();
            $img = file_get_contents($postData['img']);
            $response = $AIP->productSearch($img, ['rn' => 1]); // 搜索最相似的一张
            if (array_key_exists('error_code', $response)) {
                $pcModel->response_data = json_encode($response); 
                $pcModel->status = self::CHECK;
            } else {
                $score = $response['result'][0]['score'];
                $minScore = Yii::$app->params['minScore'];
                $maxScore = Yii::$app->params['maxScore'];
                if ($score > $maxScore) {
                    $pcModel->status = self::NO_PASS;// 审核不通过
                    $msg = '产品图片重复，请核实后上传';
                } elseif ($score > $minScore && $score < $maxScore) {
                    $pcModel->status = self::CHECK;// 人工审核
                    $msg = '图库存在相似产品图，需人工审核';
                } else {
                    $pcModel->status = self::PASS;// 审核通过
                    $msg = '审核通过';
                }
            }
            // 如果不重复则添加到图库
            if ($pcModel->status != self::NO_PASS) {
                $addRes = $AIP->productAdd($img, ['brief' => $postData['name']]);
                if (array_key_exists('error_code', $addRes)) {
                    $pcModel->response_data = $addRes;
                }
                $pcModel->cont_sign = $addRes['cont_sign'];
            }
            // 保存数据
            if ($pcModel->save(false)) {
                if ($pcModel->status == self::PASS) {
                    return $this->jsonReturn('', $msg);
                } else {
                    return Error::errorJson(400, $msg);
                }
            } else {
                return Error::errorJson(500);
            }
        } else {
            return Error::validError($pcModel);
        }
    }

    public function actionUpdate()
    {
        $pcModel = new ProductCheck();
        $id = Yii::$app->request->get('id');
        $params = Yii::$app->request->bodyParams;
        $pcModel->setScenario('update');
        if ($pcModel->load($params, '') && $pcModel->validate()) {
            $check = ProductCheck::findOne($id);
            $check->update_time = time();
            $check->update_by = $this->uid;
            $check->status = $params['status'];

            return ($check->update() ? $this->jsonReturn() : Error::errorJson(500)); 
        } else {
            return Error::validError($pcModel);
        }
    }

    /**
     * @fun 软删除产品排重检查
     *
     * @param id 产品排重表id
     *
     * @author YXH
     * @date 2018/06/05
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $pcModel = ProductCheck::find()->
            andWhere(['id' => Yii::$app->request->get('id')])->one();
        $pcModel->is_del = 1;
        // 不需要数据验证
        if ($pcModel->save(false)) {
            return $this->jsonReturn();
        } else {
            return Error::errorJson(500);
        }
    }

}
