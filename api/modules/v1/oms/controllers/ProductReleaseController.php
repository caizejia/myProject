<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\Crontab;
use api\models\Message;
use api\models\ProductLog;
use api\models\Product;
use api\models\ProductSku;
use api\models\ProductRelease;
use api\models\Promotion;
use api\models\SkuReleaseRelation;
use api\models\User;
use api\components\Error;
use api\components\Funs;

/**
 * 发布产品
 */
class ProductReleaseController extends BaseController
{
    /**
     * @fun 发布产品列表
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author YXH 
     * @date 2018/06/06
     */
    public function actionIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询数据
        $fields = [
            'id', 'pid', 'name', 'country', 'spu_code', 'attr_list', 
            'img_list','temp_type', 'create_time', 'disable'
        ];
        $prModel = new ProductRelease();
        $data = $prModel->getList($fields, '', $length, $offset);
        $pagination = Funs::get_page_info($prModel, '', $offset, $length);

        return $this->jsonReturn($data, '', $pagination);
    }

    /**
     * @fun 广告投放列表
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author jiwei
     * @date 2018/06/13
     */
    public function actionFbIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询数据
        $query = ProductRelease::find()
            ->from(ProductRelease::tableName().' AS PR')
            ->With('product')
            ->where(['PR.is_del' => 0])
            ->where(['PR.push_status' => 0]);
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
     * @fun 查看发布产品表详情
     *
     * @param id 表id
     *
     * @author YXH 
     * @date 2018/06/06
     */
    public function actionDetail()
    {
        // 接收参数
        $id = Yii::$app->request->get('id', 0);
        // 查询分类数据
        $fields = ['id', 'name', 'country', 'attr_list', 'img_list', 'temp_type',
            'domain', 'img_list', 'temp_type', 'domain', 'host', 'parameter', 'description',
        ];
        $where = ['is_del' => 0, 'id' => $id];
        $data = ProductRelease::getOne($fields, $where);

        $fields = ['id', 'sku_code', 'promotion_type', 'attr_one', 'price'];
        $where = ['is_del' => 0, 'p_r_id' => $id];
        $skuData = SkuReleaseRelation::getSkuList($fields, $where);
        $data['sku_data'] = json_encode($skuData);
        $pData = ProductRelease::getDetail($id);
        $data['attr_list'] = $pData['attr_list'];

        return $this->jsonReturn($data);
    }

    /**
     * @fun 新增发布产品
     *
     * @author YXH
     * @date 2018/06/06
     */
    public function actionCreate()
    {
        // 实例化
        $prModel = new ProductRelease();
        $prModel->setScenario('create');
        $postData = Yii::$app->request->post();
        $pid = $postData['pid'];
        $pModel = new Product();
        $pData = $pModel->getDetail($pid);
        $attrList = $pData['attr_list'];
        $str = $attrList;
        foreach ($postData['key_val'] as $v) {
            $str = str_replace($v['cn'], $v['value'], $str);
        }
        $postData['attr_list'] = $str;
        $postData['img_list'] = json_encode($postData['img_list']);
        foreach ($postData['sku_data'] as &$k) {
            foreach ($postData['key_val'] as $v) {
                $k['sku_attribute'] = str_replace($v['cn'], $v['value'], $k['sku_attribute']);
            }
            $temp = explode(',', $k['sku_attribute']);
            foreach ($temp as $key) {
                $temp2 = explode(':', $key);
                $bb[$temp2[0]] = $temp2[1];
            }
            $k['sku_attribute'] = json_encode($bb, JSON_UNESCAPED_UNICODE);
        }
        // 数据验证
        if ($prModel->load($postData, '') && $prModel->validate()) {
            // 保存数
            $prModel->create_time = $prModel->update_time = time();
            $prModel->create_by = $prModel->update_by = $this->uid;
            if ($prModel->save(false)) {
                $prId = $prModel->id;
                $srModel = new SkuReleaseRelation();
                $srModel->add($postData['sku_data'], $prId);
                if (array_key_exists('type', $postData['sku_data'][0])) {
                    $promotionModel = new Promotion();
                    $promotionModel->add($postData['sku_data'], $pid);
                }

                return $this->jsonReturn();
            } else {
                return Error::errorJson(500);
            }
        } else {
            // 参数验证失败
            return Error::validError($prModel);
        }
    }

    /**
     * @fun 更新发布产品信息
     *
     * @param id 发布产品id
     *
     * @author YXH
     * @date 2018/06/06
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $putData = Yii::$app->request->bodyParams;
        $model = ProductRelease::findOne($id);
        $model->setScenario('update');
        $model->pid = $putData['pid'];
        $model->name = $putData['name'];
        $model->country = $putData['country'];
        $model->temp_type = $putData['temp_type'];
        $model->domain = $putData['domain'];
        $model->host = $putData['host'];
        $model->parameter = $putData['parameter'];
        $model->description = $putData['description'];
        $model->img_list = json_encode($putData['img_list']);
        $attrList = $putData['attr_list'];
        $str = $attrList;
        foreach ($putData['key_val'] as $v) {
            $str = str_replace($v['cn'], $v['value'], $str);
        }
        $model->attr_list = $str;

        return $model->update() ? $this->jsonReturn() : Error::errorJson(500);
    }

    public function actionSale()
    {
        $id = Yii::$app->request->get('id');
        $params = Yii::$app->request->bodyParams;
        $model = ProductRelease::findOne($id);
        $model->update_time = time();
        $model->update_by = $this->uid;
        $model->disable = $params['disable'];

        return $model->update() ? $this->jsonReturn() : Error::errorJson(500);
    }

    /**
     * @fun 软删除发布产品
     *
     * @param id 发布产品排重表id
     *
     * @author YXH
     * @date 2018/06/06
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $model = ProductRelease::findOne($id);
        $model->is_del = 1;

        return $model->update() ? $this->jsonReturn() : Error::errorJson(500);
    }

    /**
     * @fun 发布产品初始化数据 
     * 
     * @author YXH
     * @date 2018/06/25
     */
    public function actionInitData()
    {
        $pid = Yii::$app->request->get('pid');
        $pModel = new Product();
        $pData = $pModel->getDetail($pid);
        $data['attr_list'] = $pData['attr_list'];
        $data['name'] = $pData['name'];
        $psModel = new ProductSku();
        $fields = ['id', 'sku_code', 'sku_attribute'];
        $where = [
            'is_del' => 0,
            'pid' => $pid,
        ];
        $skuData = $psModel->getSkuList($fields, $where);
        foreach ($skuData as &$v) {
            $v['sku_attribute'] = str_replace('"', '', trim($v['sku_attribute'], '{}'));
        }
        $data['sku_data'] = $skuData;
        $data['pid'] = $pid;

        return $this->jsonReturn($data);
    }

    /**
     * @fun 生成投放链接
     *
     * @param
     *
     * @author jiwei
     * @date 2018/06/13
     */
    public function actionAjaxLink()
    {
        $data = Yii::$app->request->post();
        $releaseModel = new ProductRelease();
        $model = $releaseModel::findOne($data['id']);
        $model->count += 1;
        $model->save();
        $this->actionProductLog('生成投放链接', $data['id'], Yii::$app->user->id, '');
        if ($data) {
            return 'http://test.orkotech.com?utm_source='.$data['utm_source'].'&utm_medium='.$data['utm_medium'].'&utm_content='.$data['utm_content'].'&utm_design='.$data['utm_design'].'&utm_ad='.$data['utm_ad'];
        } else {
            // 参数验证失败
            $error = $model->errors;
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
     * @fun 记录产品的操作日志
     *
     * @param
     *
     * @author jiwei
     * @date 2018/06/13
     */
    public function actionProductLog($action, $pid, $user, $remark)
    {
        $productLog = new ProductLog();
        $productLog->attributes = [
                'pid' => $pid,
                'action_user' => $user,
                'action' => $action,
                'action_ip' => '127.0.0.1',
                'log_time' => time(),
                'remark' => $remark,
        ];
        $productLog->save();
    }

    /**
     * @fun 下架产品
     *
     * @param
     *
     * @author jiwei
     * @date 2018/06/13
     */
    public function actionAjaxDisable()
    {
        $data = Yii::$app->request->post();
        if (empty($data)) {
            return Error::errorJson(400, '参数为空!');
        }
        $crontabModel = new Crontab();
        $messageModel = new Message();
        $crontabModel->attributes = [
          'user_id' => Yii::$app->user->id,
           'status' => 0,
           'create_time' => date('Y-m-d H:i:s'),
        ];
        if ($crontabModel->save()) {
            $user = User::findOne(Yii::$app->user->id);
            $mail = Yii::$app->mailer->compose();
            $mail->setFrom('support@wowmall.store');
            $mail->setTo('752879893@qq.com');
            $mail->setSubject('产品下架');
            $mail->setTextBody('产品id'.$data['id'].'24小时后即将下架,请处理产品');
            if ($mail->send()) {
                $messageModel->attributes = [
                  'user_id' => Yii::$app->user->id,
                   'title' => '产品24小时后下架,请及时处理.',
                    'message' => '产品id'.$data['id'].'24小时后即将下架,请处理产品',
                    'create_date' => date('Y-m-d H:i:s'),
                    'is_read' => 0,
                    'is_del' => 0,
                    'create_user' => 1,
                    'time' => date('Y-m-d H:i:s', strtotime('+1 day')),
                ];
                if ($messageModel->save()) {
                    $this->actionProductLog('下架产品', $data['id'], Yii::$app->user->id, '');

                    return $this->jsonReturn();
                } else {
                    // 参数验证失败
                    $error = $crontabModel->errors;
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
            };
        } else {
            // 参数验证失败
            $error = $crontabModel->errors;
            if (empty($error)) {
                $msg = '没有传参，无法验证数据';
            } else {
                $msg = '';
            }
            foreach ($error as $key) {
                $msg .= $key[0].' ';
            }

            return Error::errorJson(403, $msg);
        };
    }
}
