<?php

namespace api\modules\v1\oms\controllers;

use Yii;
use yii\web\Controller;
use mdm\admin\components\Helper;
use api\components\logistics\Logistics;
use api\components\logistics\AFLStrategy;
use api\components\logistics\OrkoKerryStrategy;
use api\models\User;
use linslin\yii2\curl;
/**
 * Default controller for the `v1` module.
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTest()
    {
        $this->testrelease();
    }

    public function testrelease()
    {
        $sql = 'SELECT name,sale_price,price,sale_end_hours,images,sku,designer,facebook,other,google,sale_city,domain,host,theme,create_time,user_id,buy_link,sale_info,next_price,declaration_cname,declaration_ename,declaration_price,ads_user FROM products';
        $data = Yii::$app->db2->createCommand($sql)->queryAll();
        foreach ($data as $v) {
            $sql = "SELECT id FROM oms_product WHERE spu = '{$v['sku']}'";
            $id = Yii::$app->db->createCommand($sql)->queryOne()['id'];
            if ($id) {
                Yii::$app->db->createCommand()->batchInsert('',
                    [
                        'pid',
                        'name',
                        'country',
                        'spu',
                        'sale_price',
                        'fake_price',
                        'attr_list',
                        'img_list',
                    ],$rows
                )->execute();
            }
        }
    }

    public function test0803()
    {
        $sql = 'SELECT id,spu FROM oms_product';
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $time = time();
        foreach ($data as $k => &$v) {
            if ($v['spu'] == '') {
                continue;
            }
            $sql = 'SELECT color,size,sku FROM product_details WHERE spu = '."'{$v['spu']}'";
            $res = Yii::$app->db2->createCommand($sql)->queryAll();
            $value = $value2 = $rows = [];
            if (is_array($res)) {
            foreach ($res as $v2) {
                $tempArr = [];
                $value[] = $v2['color'];
                $value2[] = $v2['size'];
                if ($v2['color']) {
                    $tempArr = ['颜色' => $v2['color']];
                }
                if ($v2['size']) {
                    $tempArr = array_merge($tempArr, ['尺寸' => $v2['size']]);
                }
                $skuAttr = json_encode($tempArr,JSON_UNESCAPED_UNICODE);
                $rows[] = [
                    $v['id'],
                    $v2['sku'],
                    $skuAttr,
                    $time
                ];
            }
            $trans = Yii::$app->db->beginTransaction();
            try {
                $tempSql = 'SELECT id FROM oms_product_sku WHERE pid = '.$v['id'];
                $isExists = Yii::$app->db->createCommand($tempSql)->queryAll();
                if (!$isExists) {
                Yii::$app->db->createCommand()->batchInsert('oms_product_sku',
                    [
                        'pid',
                        'sku',
                        'sku_attribute',
                        'create_time',
                    ],
                    $rows)->execute();
                }
                $value = array_filter($value);
                $value2 = array_filter($value2);
                $arr = [
                    [
                        'attr' => '颜色',
                        'value' => $value,
                    ],
                ];
                if ($value2 != []) {
                    $arr = array_merge($arr,
                        [[
                            'attr' => '尺寸',
                            'value' => $value2,
                        ]]
                    );
                }
                if (strlen(json_encode($arr,JSON_UNESCAPED_UNICODE)) < 512) {
                Yii::$app->db->createCommand()->update('oms_product',
                    [
                        'attr_list' => json_encode($arr,JSON_UNESCAPED_UNICODE),
                        ],"spu = '{$v['spu']}'")->execute();
                }
                $trans->commit();// 提交
            } catch(\Exception $e) {
                $trans->rollBack();// 回滚
                var_dump($e->getMessage());exit;
            }
            }
        }
        var_dump($data);exit;
    }

    public function test0802()
    {
        $sql = 'SELECT * FROM product_center';
        $data = Yii::$app->db2->createCommand($sql)->queryAll();
        $cateList = Yii::$app->db->createCommand('SELECT id,value FROM oms_category')->queryAll();
        $list = [];
        foreach ($cateList as $v) {
            $list[$v['value']] = $v['id'];
        }
        $sexArr = [
            'T' => 0,
            'M' => 1,
            'F' => 2,
        ];

        $rows = [];
        foreach ($data as $v) {
            $rows[] = [
                $list[$v['classify']],
                $v['product_type'] == 'M' ? 1 : 0,
                $v['open'],
                $sexArr[$v['sex']],
                $v['name'],
                $v['spu'],
                json_encode([$v['image']]),
                ($v['user_id'] == null) ?? '',
                ($v['create_time'] == null) ?: '',
            ];
        }

        $id = Yii::$app->db->createCommand()->batchInsert('oms_product', 
        [
            'cid',
            'is_sensitive',
            'open_level',
            'sex',
            'name',
            'spu',
            'img_list',
            'create_by',
            'create_time',
        ], 
        $rows)->execute();

        var_dump($id);exit;

    }

    //$sql = 'SELECT P.*,GROUP_CONCAT(PD.*) FROM products P 
        //    LEFT JOIN product_details PD ON P.sku = PD.spu
        //    WHERE P.sku = '."'A00005PF'";
        //$sql = 'SELECT * FROM product_center';
        //$data = Yii::$app->db2->createCommand($sql)->queryAll();
        //$cateList = Yii::$app->db->createCommand('SELECT id,value FROM oms_category')->queryAll();
        //$list = [];
        //foreach ($cateList as $v) {
        //    $list[$v['value']] = $v['id'];
        //}
        //$sexArr = [
        //    'T' => 0,
        //    'M' => 1,
        //    'F' => 2,
        //];

        //$rows = [];
        //foreach ($data as $v) {
        //    $rows[] = [
        //        $list[$v['classify']],
        //        $v['product_type'] == 'M' ? 1 : 0,
        //        $v['open'],
        //        $sexArr[$v['sex']],
        //        $v['name'],
        //        $v['spu'],
        //        json_encode([$v['image']]),
        //        ($v['user_id'] == null) ?? '',
        //        ($v['create_time'] == null) ?: '',
        //    ];
        //}

        //$id = Yii::$app->db->createCommand()->batchInsert('oms_product', 
        //[
        //    'cid',
        //    'is_sensitive',
        //    'open_level',
        //    'sex',
        //    'name',
        //    'spu',
        //    'img_list',
        //    'create_by',
        //    'create_time',

        //    //'keyword',
        //    //'sale_price',
        //    //'price',
        //    //'sex',
        //    //'cost',
        //    //'description',
        //    //'parameter',
        //    //'attr_list',
        //    //'attr_id_list',
        //    //'think',
        //    //'remark',
        //    //'declare_cname',
        //    //'declare_ename',
        //    //'declare_price',
        //    //'declare_code',
        //    //'update_time',
        //    //'update_by',
        //], 
        //$rows)->execute();

        //var_dump($id);exit;

        // 实例化curl 发起请求
        //$curl = new curl\Curl();
        //$response = $curl->setPostParams($reqData)
        //    ->setHeaders([
        //        'Content-Type' => 'application/json',
        //        'app_id' => $this->appId,
        //        'app_key' => $this->appKey,
        //    ])
        //    ->post($this->url);
        //$params; 
        // 2018/08/02
        //$sql = 'SELECT id FROM user WHERE status = 10';
        //$c = Yii::$app->db->createCommand($sql);
        //$data = $c->queryAll();
        //$time = time();
        //foreach ($data as &$v) {
        //    $v['item_name'] = '任务模块临时角色';
        //    $v['created_at'] = $time;
        //}
        //Yii::$app->db->createCommand()->batchInsert(
        //    'auth_assignment',
        //    [
        //        'user_id', 'item_name','created_at' 
        //    ],$data)->execute();
        // 2018/07/19
        //$sql = 'SELECT * FROM admin_user';
        //$c = Yii::$app->db->createCommand($sql);
        //$data = $c->queryAll();
        //$time = time();
        //foreach ($data as &$v) {
        //    $v['realname'] = $v['name'];
        //    $v['dingding_id'] = $v['ding_id'] ?? 0;
        //    $v['created_at'] = $v['created_at'] ?? time();
        //    $v['updated_at'] = $v['updated_at'] ?? time();
        //    $v['website_id'] = $v['website_id'] ?? 0;
        //    $v['warehouse_id'] = $v['w_id'];
        //    $v['access_token'] = uniqid();
        //    unset($v['ding_id'], $v['name'], $v['w_id']);
        //}
        //Yii::$app->db->createCommand()->batchInsert(
        //    'user', 
        //    [
        //        'id', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'status', 
        //        'created_at', 'updated_at', 'website_id', 'is_super_user', 'group_id', 
        //        'is_leader', 'service_area', 'classification',  'realname', 'dingding_id', 'warehouse_id','access_token'    
        //    ], $data)->execute();
        // end
        //
        //$help = new Helper();
        //$routeList = array_keys($help->getRoutesByUser(1));
        //var_dump($routeList);exit;
        // 2018/07/15
        //$logistics = new Logistics(new OrkoKerryStrategy());
        //$data = $logistics->pushOrder(1);
        //var_dump($data);exit;
        // end
}
