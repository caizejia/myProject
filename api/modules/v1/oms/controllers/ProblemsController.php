<?php

namespace api\modules\v1\oms\controllers;

use api\models\Order;
use api\models\Problems;
use api\models\ProblemsLog;
use api\models\Product;
use Yii;
use api\components\Error;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;
use yii\web\NotFoundHttpException;

class ProblemsController extends BaseController
{
    /**
     * @fun 问题件
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
        $query = Problems::find()
            ->where(['is_del' => '0']);
        $data = $query
            ->limit($length)
            ->offset($offset)
            ->asArray()->all();
        $count = $query->count();
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = $count;

        return $this->jsonReturn($res);
    }

    /**
     * @fun 新增问题件
     *
     * @author JW
     * @date 2018/06/01
     */
    public function actionCreate()
    {
        // 实例化
        $sModel = new Problems();

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
     * @fun 更新问题件
     *
     * @param
     *
     * @author JW
     * @date 2018/06/01
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $sModel = Problems::find()->
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
     * @fun 软删除问题件
     *
     * @param id 产品分类id
     *
     * @author JW
     * @date 2018/06/01
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $sModel = Problems::find()->
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

    /**
     * 问题表导入.
     */
    public function actionImport()
    {
        $model = new Problems();
        $orderModel = new Order();

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            $file_name = $model->upload();
            $data = Excel::import('uploads/'.$file_name, [
                'getOnlySheet' => 'Sheet1',
                'setFirstRecordAsKeys' => true,
            ]);
            unlink('uploads/'.$file_name);
            $updates = [];

            foreach ($data as $row) {
                $problem = $model->find()->where(['track_number' => $row['物流单号']])->one();
                $order = $orderModel->find()->where(['lc_number' => $row['物流单号']])->one();
                if ($problem) {
                    if (!empty(trim($row['问题类型']))) {
                        $problem->problem = intval($row['问题类型']);
                    }
                    if (!empty(trim($row['问题状态']))) {
                        $problem->status = intval($row['问题状态']);
                    }
                    if (!empty(trim($row['问题描述']))) {
                        $problem->description = $row['问题描述'];
                    }

                    if ($order) {
                        $problem->order_id = $order->id;
                    }

                    if ($problem->save()) {
                        $updates['success'][] = $row + ['update' => '成功'];
                    } else {
                        $updates['error'][] = $row + ['update' => '<span style="color:red;">失败'.print_r($problem->getErrors(), true).'</span>'];
                    }
                } else {
                    $model->setIsNewRecord(true);
                    unset($model->id);
                    $model->attributes = [
                        'order_id' => $order->id,
                        'problem' => intval($row['问题类型']),
                        'status' => intval($row['问题状态']),
                        'description' => $row['问题描述'],
                        'track_number' => $row['物流单号'],
                    ];
                    if ($model->save()) {
                        $updates['success'][] = $row + ['update' => '成功'];
                    } else {
                        $updates['error'][] = $row + ['update' => '<span style="color:red;">失败'.print_r($model->getErrors(), true).'</span>'];
                    }
                }
            }

            return $this->render('import', [
                'model' => $model,
                'list' => $updates,
            ]);
        } else {
            return $this->render('import', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 发送邮件.
     *
     * @param $id
     */
    public function actionSendmail($id)
    {
        $orderModel = new Order();
        $order = $orderModel->findOne($id);
        $product = Product::findOne($order->website);
        $tpl = Yii::$app->request->get('tpl');
        $logModel = new ProblemsLog();

        if ($order->email) {
            Yii::$app->mailer->compose($tpl, ['order' => $order, 'product' => $product])
                ->setFrom('customer@angeltmall.com')
                ->setTo(trim($order->email))
                ->setSubject('Angeltmall')
                ->send();
            $action_date = date('Y-m-d');
            $logModel->attributes = [
                'problems_id' => Yii::$app->request->get('pid'),
                'action_type' => 1,
                'remarks' => Yii::$app->request->get('remarks'),
                'action_tpl' => $tpl,
                'action_date' => $action_date,
            ];
            $logModel->save();
            echo '邮件发送成功';
        } else {
            echo '邮件地址为空！';
        }
    }

    /**
     * 标记电话确认.
     */
    public function actionFlagCall($id)
    {
        $logModel = new ProblemsLog();
        $action_date = date('Y-m-d');
        $logModel->attributes = [
            'problems_id' => $id,
            'action_type' => 2,
            'remarks' => Yii::$app->request->get('remarks'),
            'action_date' => $action_date,
            'is_download' => 0,
        ];
        if ($logModel->save()) {
            echo 200;
        } else {
            echo 500;
        }
    }

    public function actionFeedback()
    {
        $id = Yii::$app->request->post('id');
        $feedback = Yii::$app->request->post('feedback');
        $logModel = new ProblemsLog();
        $log = $logModel->findOne($id);
        $log->feedback = $feedback;
        if ($log->save()) {
            echo '更新成功';
        }
    }

    /**
     * 电话反馈导入
     * 使用订单号与电话检测数据是否有误.
     */
    public function actionImportFeedback()
    {
        $model = new Problems();
        $orderModel = new Order();

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            $file_name = $model->upload();
            $data = Excel::import('uploads/'.$file_name, [
                'getOnlySheet' => 'Sheet1',
                'setFirstRecordAsKeys' => true,
            ]);
            unlink('uploads/'.$file_name);
            $updates = [];

            foreach ($data as $row) {
                $order_id = trim($row['订单号']);
                $mobile = trim($row['电话']);

                $order = $orderModel->findOne(['id' => $order_id, 'mobile' => $mobile]);
                if ($order) {
                    $problem = $model->findOne(['order_id' => $order_id]);
                    if ($problem) {
                        $probleLog = ProblemsLog::find()->where(['problems_id' => $problem->id])->orderBy('id DESC')->one();
                        if ($probleLog) {
                            $probleLog->feedback = $row['客户反馈'];
                            $probleLog->action_date = date('Y-m-d H:i:s');
                        } else {
                            $probleLog = new ProblemsLog();
                            unset($probleLog->id);
                            $probleLog->attributes = [
                                'problems_id' => $problem->id,
                                'action_type' => '2',
                                'feedback' => $row['客户反馈'],
                                'action_date' => date('Y-m-d H:i:s'),
                            ];
                        }
                        if ($row['处理状态']) {
                            $problem->status = $problem->statusWorldToId[$row['处理状态']];
                        }
                        if ($probleLog->save() && $problem->save()) {
                            $updates['success'][] = $row + ['update' => '成功'];
                        } else {
                            $updates['error'][] = $row + ['update' => '<span style="color:red;">失败'.print_r($probleLog->getErrors(), true).print_r($problem->getErrors(), true).'</span>'];
                        }
                    } else {
                        $updates['error'][] = $row + ['update' => '<span style="color:red;">问题件不存在</span>'];
                    }
                } else {
                    $updates['error'][] = $row + ['update' => '<span style="color:red;">失败(订单号或电话号码错误)</span>'];
                }
            }

            return $this->render('import_feedback', [
                'model' => $model,
                'list' => $updates,
            ]);
        } else {
            return $this->render('import_feedback', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 订单与产品信息获取AJAX接口.
     */
    public function actionOrderJson()
    {
        $orderModel = new Order();
        $order_id = Yii::$app->request->get('order_id');
        $id = (int) Yii::$app->request->get('id');
        $order = $orderModel->findOne($order_id);
        $problem = $this->findModel($id);
        $product = Product::findOne($order->website);
        $url = 'http://'.$product->host.'.'.$product->domain;
        echo json_encode($order->attributes + ['problem' => $problem->description, 'new_price' => $problem->new_price, 'url' => $url]);
    }

    /**
     * 电话沟通更新问题件.
     */
    public function actionUpdateProblem()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));
        $model->new_price = Yii::$app->request->post('new_price');
        $model->status = Yii::$app->request->post('status');

        $order = Order::findOne($model->order_id);
        $order->city = Yii::$app->request->post('city');
        $order->district = Yii::$app->request->post('district');
        $order->address = Yii::$app->request->post('address');

        $logModel = new ProblemsLog();
        $action_date = date('Y-m-d');
        $logModel->attributes = [
            'problems_id' => $model->id,
            'action_type' => 2,
            'remarks' => Yii::$app->request->post('comment'),
            'feedback' => Yii::$app->request->post('comment'),
            'action_date' => $action_date,
            'is_download' => 1,
        ];

        if ($model->save() && $order->save() && $logModel->save()) {
            echo 200;
        } else {
            echo 500;
        }
    }

    public function actionUpdateColumn()
    {
        $id = Yii::$app->request->post('id');
        $column = Yii::$app->request->post('column');
        $value = Yii::$app->request->post('value');
        $model = $this->findModel($id);
        $model->$column = $value;
        if ($model->save()) {
            echo 200;
        } else {
            echo 500;
        }
    }

    /**
     * Finds the Problems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Problems the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Problems::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
