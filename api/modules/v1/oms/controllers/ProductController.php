<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use api\models\Product;
use api\models\ProductSku;
use api\models\ProductCheck;
use api\components\Error;
use api\components\Funs;
use api\models\Category;
use api\models\Brand;
use api\models\Supplier;
use api\models\AttributeKey;

/**
 * 产品
 */
class ProductController extends BaseController
{
    /**
     * @fun 产品列表
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
        $length = Yii::$app->request->get('length', 20);
        // 查询数据
        $fields = [
            'id', 'name', 'cid', 'bid', 'sid_list', 'spu_code', 'cost', 
            'create_time', 'attr_list', 'img_list', 'open_level'
        ];
        $pModel = new Product();
        $data = $pModel->getList($fields, '', $length, $offset);
        $pagination = Funs::get_page_info($pModel, '', $offset, $length);

        return $this->jsonReturn($data, '', $pagination);
    }

    /**
     * @fun 查看产品表详情
     *
     * @param id 表id
     *
     * @author YXH 
     * @date 2018/06/05
     */
    public function actionDetail()
    {
        // 接收参数
        $id = Yii::$app->request->get('id', 0);
        // 查询分类数据
        $fields = [
            'id', 'name', 'cid', 'bid', 'sid_list', 'spu_code', 'cost', 
            'attr_list','sex', 'declare_cname', 'declare_ename', 'declare_code',
            'declare_price', 'remark','description', 'think', 'create_time', 
            'create_by', 'is_sensitive', 'keyword', 'open_level', 'img_list'
        ];
        $where = ['is_del' => 0, 'id' => $id];
        $data = Product::getOne($fields, $where);
        $data['username'] = $data['user']['username'];
        $data['category_name'] = $data['category']['name'];
        $data['brand_name'] = $data['brand']['name'];
        $data['cate_list'] = Category::categoryList(); 
        $selectArr = [];
        Category::getSelectedOption($data['category']['id'], $selectArr);
        $data['select_option'] = json_encode($selectArr);

        unset($data['user'], $data['category'], $data['brand']);

        return $this->jsonReturn($data);
    }

    /**
     * @fun 新增产品
     *
     * @author YXH
     * @date 2018/06/05
     */
    public function actionCreate()
    {
        // 实例化
        $pModel = new Product();
        $pModel->setScenario('create');
        $postData = Yii::$app->request->post();
        $postData['attr_list'] = json_encode($postData['attr_list'], JSON_UNESCAPED_UNICODE);
        $postData['sid_list'] = json_encode($postData['sid_list']);
        $postData['img_list'] = json_encode($postData['img_list']);
        // 数据验证
        if ($pModel->load($postData, '') && $pModel->validate()) {
            // 保存数据
            $time = time();
            $pModel->create_time = $pModel->update_time = $time;
            $pModel->create_by = $pModel->update_by = $this->uid;
            // 开启事务
            $trans = Yii::$app->db->beginTransaction();
            try{
                $pModel->save(false);
                $pid = $pModel->id;// 最后插入id
                $psModel = new ProductSku();
                $psData = [
                    'pid' => $pid,
                    'attr_list' => json_decode($postData['attr_list'], true),
                    'uid' => $this->uid,
                ];
                // product sku 
                $psModel->add($psData);
                $trans->commit();

                return $this->jsonReturn();
            } catch(\Exception $e) {
                $trans->rollBack();// 回滚
                // TODO 记录错误信息
                return Error::errorJson(500);
            }
        } else {
            return Error::validError($pModel);
        }
    }

    /**
     * @fun 更新产品信息
     *
     * @param id 产品id
     *
     * @author YXH
     * @date 2018/06/05
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $putData = Yii::$app->request->bodyParams;
        $model = Product::findOne($id);
        $model->setScenario('update');
        $model->name = $putData['name'];
        $model->cid = $putData['cid'];
        $model->cost = $putData['cost'];
        $model->keyword = $putData['keyword'];
        $model->remark = $putData['remark'];
        $model->sex = $putData['sex'];
        $model->sid_list = $putData['sid_list'];
        $model->description = $putData['description'];
        $model->is_sensitive = $putData['is_sensitive'];
        $model->think = $putData['think'];
        $model->open_level = $putData['open_level'];
        $model->declare_cname = $putData['declare_cname'];
        $model->declare_ename = $putData['declare_ename'];
        $model->declare_code = $putData['declare_code'];
        $model->declare_price = $putData['declare_price'];
        $model->update_time = time();
        $model->update_by = $this->uid;
        $imgArr = json_decode($putData['img_list'], true);
        foreach ($imgArr as &$v) {
            $v = $v['url'];
        }
        $model->img_list = json_encode($imgArr);
        $model->attr_list = $putData['attr_list'];

        return ($model->update() ? $this->jsonReturn() : Error::errorJson(500)); 
    }

    /**
     * @fun 软删除产品
     *
     * @param id 产品排重表id
     *
     * @author YXH
     * @date 2018/06/05
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $model = Product::findOne($id);
        $model->is_del = 1;

        return ($model->update() ? $this->jsonReturn() : Error::errorJson(500));
    }

    /**
     * @fun 新增产品初始化数据 包括（分类，品牌，供应商，产品规格）
     */
    public function actionInitData()
    {
        $cateList = Category::categoryList();
        $brandList = Brand::selectList();
        $supplierList = Supplier::selectList();
        $attrList = AttributeKey::checkList();
        // 获取产品检查详情
        $checkId = Yii::$app->request->get('pid');
        $checkData = ProductCheck::getOne(['id', 'name', 'img'], ['id' => $checkId]);

        $data['cate_list'] = $cateList;
        $data['brand_list'] = $brandList;
        $data['supplier_list'] = $supplierList;
        $data['attr_list'] = $attrList;
        $data['check_data'] = $checkData;

        return $this->jsonReturn($data);
    }
}
