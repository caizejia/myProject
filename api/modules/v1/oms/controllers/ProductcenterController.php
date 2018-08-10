<?php

namespace api\modules\v1\oms\controllers;

use api\models\ProductCenter;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use api\components\Error;
use api\components\Image\AipImageSearch;

class ProductCenterController extends BaseController
{

    /**
     * @fun 产品中心页面
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author jiwei
     * @date 2018/05/29
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        if (Yii::$app->request->isGet) {
            $data = ProductCenter::find()
                ->orderBy('id ASC')
                ->offset($offset)
                ->limit($length)
                ->asArray()->all();
            return $data;
        } else {
            return Error::errorJson(400);
        }
    }

    /**
     * @fun 添加产品中心
     *
     * @author jiwei
     * @date 2018/05/29
     * @return array|\yii\db\ActiveRecord[]
     * @return fix|array
     */
    public function actionCreate()
    {
        $model = new ProductCenter();

        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->post();
            $model->attributes = [
                'spu' => ProductCenter::sku($request, 1),
                'user_id' => Yii::$app->user->id,
                'classify' => $request['classify'],
                'open' => 0,
                'product_type' => $request['product_type'],
                'sex' => $request['sex'],
                'name' => $request['name'],
                'repeat_status' => 0,
                'image' => $request['image']
            ];
            if ($model->save()) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(400);
            }
        } else {
            return Error::errorJson(400);
        }
    }

    /**
     * @fun 更新产品中心
     *
     * @author jiwei
     * @date 2018/05/29
     * @return array|\yii\db\ActiveRecord[]
     * @return fix|array
     */
    public function actionUpdate()
    {
        // 实例化
        $Model = ProductCenter::findOne(Yii::$app->request->get('id'));
        // 数据验证
        if ($Model->load(Yii::$app->request->bodyParams, '')) {
            if ($Model->validate()) {
                // 保存数据
                $res = $Model->save(false);
                if ($res) {
                    return $this->jsonReturn();
                } else {
                    return Error::errorJson(500);
                }
            } else {
                // 参数验证失败
                $error = $Model->errors;
                foreach ($error as $key) {
                    $msg = $key[0];
                }
                return Error::errorJson(403, $msg);
            }

        } else {
            return Error::errorJson(400, '数据加载失败!');
        }
    }

    /**
     * @fun 提交产品审核
     *
     * @author jiwei
     * @date 2018/05/30
     * @return array|\yii\db\ActiveRecord[]
     * @return fix|array
     */
    public function actionUpdateRepeat()
    {
        // 实例化
        $Model = ProductCenter::findOne(Yii::$app->request->post('id'));
        $repeat = Yii::$app->request->post('repeat');
        $Model->attributes = [
            'repeat_status' => $repeat,
        ];
        if ($Model->validate()) {
            // 保存数据
            $res = $Model->save(false);
            if ($res) {
                $src = ProductCenter::SearchPictureDetection($Model->name, $Model->id, $Model->image);
                if (empty($src['result'])) {
                    ProductCenter::AddPictureDetection($Model->name, $Model->id, $Model->image);
                } else {
                    $data = json_decode($src['result'][0]['brief'], true);
                    if ($src['result']['score'] < 0.6) {
                        ProductCenter::updateAll(['repeat_status' => 3, 'remark' => '与产品中心ID' . $data['id'] . '重复' . '相似度' . $src['result']['score']], ['id' => Yii::$app->request->post('id')]);
                    } else {
                        ProductCenter::updateAll(['repeat_status' => 2, 'remark' => '与产品中心ID' . $data['id'] . '重复' . '相似度' . $src['result']['score']], ['id' => Yii::$app->request->post('id')]);
                    }
                }

                return $this->jsonReturn('', '提交审核成功!');

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
     * @fun 产品中心审核人员页面
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author jiwei
     * @date 2018/05/29
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionRepeatIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        if (Yii::$app->request->isGet) {
            $data = ProductCenter::find()
                ->where(['repeat_status' => 3])
                ->orderBy('id ASC')
                ->offset($offset)
                ->limit($length)
                ->asArray()->all();
            return $data;
        } else {
            return Error::errorJson(400);
        }
    }


    /**
     * @fun 审核人员产品审核
     *
     * @author jiwei
     * @date 2018/05/30
     * @return array|\yii\db\ActiveRecord[]
     * @return fix|array
     */
    public function actionRepeatStatus()
    {
        // 实例化
        $Model = ProductCenter::findOne(Yii::$app->request->post('id'));
        $repeat = Yii::$app->request->post('repeat');
        $Model->attributes = [
            'repeat_status' => $repeat,
        ];
        if ($Model->validate()) {
            // 保存数据
            $res = $Model->save(false);
            if ($res) {
                return $this->jsonReturn('审核成功!');
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


}

