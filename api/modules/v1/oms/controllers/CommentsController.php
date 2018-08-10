<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\Comments;
use api\components\Error;

/**
 * 产品评论
 */
class CommentsController extends BaseController
{
    /**
     * @fun 产品评论列表
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionIndex()
    {
        $offset = Yii::$app->request->get('offset');
        //$length = Yii::$app->request->get('length', 10);
        $length = 10;
        if($offset>0&&gettype($offset)=='integer'){
            $offset = ($offset-1)*10;
        }else{
            $offset = 0;
        }
        
        // 查询评论数据
        $query = Comments::find();
        $data = $query->select(['id', 'pid', 'name', 'mobile', 'country', 'type', 'create_time'])
            ->andWhere(['is_del'=>0])
            ->limit($length)
            ->offset($offset)
            ->asArray()->all();
        $count = $query->count();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
        }
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = ceil($count/$length);

        return $this->jsonReturn($res);
    }

    /**
     * @fun 新增产品评论
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionCreate()
    {
        // 实例化
        $cModel = new Comments();
        $cModel->setScenario('create');
        // 数据验证
        if ($cModel->load(Yii::$app->request->post(), '') && $cModel->validate()) {
            // 保存数据
            $cModel->create_by = 1;
            $cModel->create_time = time();
            $cModel->ip = $_SERVER['REMOTE_ADDR'];
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
     * @fun 更新产品评论信息
     *
     * @param id 评论id
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $cModel = Comments::find()->andWhere(['id' => Yii::$app->request->get('id')])->one();
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
     * @fun 软删除产品评论
     *
     * @param id 评论id
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $cModel = Comments::find()->andWhere(['id' => Yii::$app->request->get('id')])->one();
        $cModel->is_del = 1;
        // 不需要数据验证
        $res = $cModel->save(false);
        if ($res) {
            return $this->jsonReturn();
        } else {
            return Error::errorJson(500);
        }
    }


    /**
     * @fun 产品评论详情
     *
     * @param id 评论id
     *
     * @author FHH
     * @date 2018/06/04
     */
    public function actionDetail()
    {
        // url上的id使用get方式获取
        $data = Comments::find()->andWhere(['id' => Yii::$app->request->get('id')])->asArray()->one();
        $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);

        if ($data) {
            return $this->jsonReturn($data);
        } else {
            return Error::errorJson(500);
        }
    }
}
