<?php

namespace api\modules\v1\oms\controllers;

use api\models\TranslationLog;
use api\models\User;
use Yii;
use api\models\Translation;
use api\components\Error;

/**
 * 翻译任务控制器.
 */
class TranslationController extends BaseController
{
    /**
     * @fun 翻译任务列表
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author YXH 
     * @date 2018/05/30
     */
    public function actionIndex()
    {
        // 接收参数
        $page = Yii::$app->request->get('page');//页码
        $pagesize = Yii::$app->request->get('pagesize');//
        $status = Yii::$app->request->get('status');
        $level = Yii::$app->request->get('level');
        // 查询分类数据
        $query = Translation::find()
            ->from(Translation::tableName().' AS T')
            ->where(['T.is_del' => '0'])
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['level' => $level])
            ->with('user')
            ->with('designer');
        $data = $query
            ->limit($pagesize)
            ->offset(($page - 1) * $pagesize)
            ->orderBy('id DESC')
            ->asArray()
            ->all();
        $count = $query->count();
        foreach ($data as &$v) {
            $v['username'] = $v['user']['username'] ?? '';
            $v['designer'] = $v['designer']['username'] ?? '';
            $v['fix_name'] = Translation::UserName($v['fix_uid']) ?? '';
            $v['design_name'] = Translation::UserName($v['design_uid']) ?? '';
            $v['status_name'] = Translation::status($v['status']) ?? '';
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['final_time'] = date('Y-m-d H:i', $v['final_time']);
            $v['fix_time'] = date('Y-m-d H:i', $v['fix_time']);
            unset($v['user']);
        }
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = $count;

        return $this->jsonReturn($res);
    }

    /**
     * @fun 查看翻译任务详情
     *
     * @param id 表id
     *
     * @author YXH 
     * @date 2018/06/01
     */
    public function actionDetail()
    {
        // 接收参数
        $id = Yii::$app->request->get('id', 0);
        // 查询分类数据
        $data = Translation::find()
            ->from(Translation::tableName().' AS T')
            ->where(['T.is_del' => '0', 'T.id' => $id])
            ->with('user')
            ->with('designer')
            ->asArray()->one();
        $data['username'] = $data['user']['username'] ?? '';
        $data['designer'] = $data['designer']['username'] ?? '';
        $data['fix_name'] = Translation::UserName($data['fix_uid']) ?? '';
        $data['design_name'] = Translation::UserName($data['design_uid']) ?? '';
        $data['status_name'] = Translation::status($data['status']) ?? '';
        $data['create_time'] = date('Y-m-d H:i', $data['create_time']);
        $data['final_time'] = date('Y-m-d H:i', $data['final_time']);
        $data['fix_time'] = date('Y-m-d H:i', $data['fix_time']);
        unset($data['user']);

        return $this->jsonReturn($data);
    }

    /**
     * @fun 新增翻译任务
     *
     * @author YXH
     * @date 2018/05/30
     */
    public function actionCreate()
    {
        // 实例化
        $tModel = new Translation();
        $tModel->setScenario('create');
        // 数据验证
        if ($tModel->load(Yii::$app->request->post(), '') && $tModel->validate()) {
            $tModel->create_time = $tModel->update_time = time();
            $tModel->update_by = $this->uid;
            // 保存数据
            $res = $tModel->save(false);
            if ($res) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(500, var_dump($tModel->getErrors()));
            }
        } else {
            // 参数验证失败
            $error = $tModel->errors;
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
     * @fun 更新翻译任务信息
     *
     * @param id 翻译任务id
     *
     * @author YXH
     * @date 2018/05/30
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $tModel = Translation::find()->
            andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();
        $tModel->setScenario('update');
        // bodyParams 获取PUT参数
        if ($tModel->load(Yii::$app->request->bodyParams, '') && $tModel->validate()) {
            $tModel->update_time = time();
            $tModel->update_by = $this->uid;
            if ($tModel->content_url) {
                $tModel->status = 2;
                $tModel->count += 1;
            }
            // 保存数据
            $res = $tModel->save(false);
            if ($res) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(500);
            }
        } else {
            // 参数验证失败
            $error = $tModel->errors;
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
     * @fun 软删除翻译任务
     *
     * @param id 翻译任务id
     *
     * @author YXH
     * @date 2018/05/30
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $tModel = Translation::find()->
            andWhere(['id' => Yii::$app->request->get('id')])->one();
        $tModel->is_del = 1;
        // 不需要数据验证
        $res = $tModel->save(false);
        if ($res) {
            return $this->jsonReturn();
        } else {
            return Error::errorJson(500);
        }
    }

    /**
     * @fun 更改审核状态
     *
     * @param id 翻译任务id
     *
     * @author jiweiz
     * @date 2018/06/12
     */
    public function actionAjaxStatus()
    {
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');
        $uid = Yii::$app->request->post('update_by');
        $unpass_reason = Yii::$app->request->post('unpass_reason');
        $TranslationModel = new Translation();
        $model = $TranslationModel::findOne($id);
        $model->setScenario('fix');
        $model->status = $status;
        $model->unpass_reason = $unpass_reason;
        if ($status == 1) {
            $model->translate_time = time();
        } else {
            $model->update_time = time();
        }
        $model->update_by = $uid;
        if ($model->save()) {
            $logModel = new TranslationLog();
            $logModel->attributes = [
                'tid' => $id,
                'action_user' => Yii::$app->user->id,
                'old_status' => $model->status,
                'new_status' => $status,
                'action_ip' => '127.0.0.1',
                'log_time' => time(),
            ];

            return $this->jsonReturn();
        } else {
            return Error::errorJson(400, '更改失败');
        }
    }

    /**
     * @fun 指派翻译任务
     *
     * @param id 翻译任务id
     *
     * @author jiwei
     * @date 2018/06/13
     */
    public function actionFixUser()
    {
        $uid = Yii::$app->request->post('fix_uid');
        $id = Yii::$app->request->post('id');
        if (empty($uid) && empty($id)) {
            return $uid;
        }
        $translationModel = new Translation();
        $model = $translationModel::findOne($id);
        $model->setScenario('fix');
        $model->fix_uid = $uid;
        if ($model->fix_time == '0') {
            $model->fix_time = time();
        }

        if ($model->save()) {
            $logModel = new TranslationLog();
            $logModel->attributes = [
                'tid' => $id,
                'action_user' => Yii::$app->user->id,
                'old_status' => 0,
                'new_status' => 5,
                'action_ip' => '127.0.0.1',
                'log_time' => time(),
            ];
            $logModel->save();

            return $this->jsonReturn();
        } else {
            return Error::errorJson(400, '更改失败');
        }
    }

    /*
     * 获取翻译人员接口
     */
    public function actionUserName()
    {
        $userModel = new User();
        $userName = $userModel::find()->where(['status' => 10])->asArray()->all();
        if ($userName) {
            return $this->jsonReturn($userName);
        } else {
            return $this->jsonReturn($userName, '暂无翻译人员');
        }
    }
}
