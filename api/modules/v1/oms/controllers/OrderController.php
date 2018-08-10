<?php

namespace api\modules\v1\oms\controllers;

use api\models\Member;
use api\models\OrderTime;
use api\models\Problems;
use api\models\Order;
use api\models\OrderLog;
use Yii;
use api\components\Error;
use yii\web\NotFoundHttpException;
use api\models\OrderGoods;
use api\models\ProductRelease;
use api\models\User;
use api\models\ChannelServices;
use api\models\ProductSku;
use api\models\SkuReleaseRelation;
use api\models\ProductDetails;
use api\models\TrackLog;

class OrderController extends BaseController
{
    /**
     * @fun 订单
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author JW
     * @date 2018/06/02
     */
    public function actionIndex()
    {
        $page = Yii::$app->request->get('page');//页码
        $pagesize = Yii::$app->request->get('pagesize');//
        // 查询供应商数据
        $query = Order::find()->where(['is_del' => 0]);
        $data = $query
            ->limit($pagesize)
            ->offset(($page - 1) * $pagesize)
            ->orderBy('id DESC')
            ->asArray()
            ->all();
        $count = $query->count();
        $res = [];
        $res['list'] = $data;
        $res['page_total'] = $count;

        return $this->jsonReturn($res);
    }

    /**
     * @fun 站点
     *
     * @author FHH
     * @date 2018/06/02
     */
    public function actionShow()
    {
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name' => 'clickid',
            'value' => Yii::$app->request->get('mobvista_clickid'),
        ]));
        $cookies->add(new \yii\web\Cookie([
            'name' => 'uuid',
            'value' => Yii::$app->request->get('mobvista_campuuid'),
        ]));
        $cookies->add(new \yii\web\Cookie([
            'name' => 'ObClickId',
            'value' => Yii::$app->request->get('ob_click_id'),
        ]));
        $cookies->add(new \yii\web\Cookie([
            'name' => 'transactionId',
            'value' => Yii::$app->request->get('transaction_id'),
        ]));
        $cookies->add(new \yii\web\Cookie([
            'name' => 'affiliateId',
            'value' => Yii::$app->request->get('affiliate_id'),
        ]));
        $cookies->add(new \yii\web\Cookie([
            'name' => 'from',
            'value' => $_SERVER['HTTP_REFERER'],
        ]));

        $utm_one = unserialize(Yii::$app->request->cookies->getValue('utm', false));
        if (Yii::$app->request->get('utm_source') || Yii::$app->request->get('utm_medium') || Yii::$app->request->get('utm_ad') || Yii::$app->request->get('utm_content') || Yii::$app->request->get('utm_design_midastouch') || Yii::$app->request->get('utm_google_pixel') || Yii::$app->request->get('utm_facebook_pixel') || Yii::$app->request->get('utm_yahoo_pixel') || Yii::$app->request->get('utm_design')) {
            $utm_design = Yii::$app->request->get('utm_design_midastouch') ? Yii::$app->request->get('utm_design_midastouch') : Yii::$app->request->get('utm_design');
            $utm = [
                'utm_source' => Yii::$app->request->get('utm_source'),
                'utm_medium' => Yii::$app->request->get('utm_medium'),
                'utm_ad' => Yii::$app->request->get('utm_ad'),
                'utm_offid' => Yii::$app->request->get('utm_offid'),
                'utm_aff' => Yii::$app->request->get('utm_aff'),
                'utm_content' => Yii::$app->request->get('utm_content'),
                'utm_design' => $utm_design,
                'utm_google_pixel' => Yii::$app->request->get('utm_google_pixel'),
                'utm_facebook_pixel' => Yii::$app->request->get('utm_facebook_pixel'),
                'utm_yahoo_pixel' => Yii::$app->request->get('utm_yahoo_pixel'),
            ];

            $expire = 7 * 24 * 3600;
            $cookies->add(new \yii\web\Cookie([
                'name' => 'utm',
                'value' => serialize($utm),
                'expire' => time() + $expire,
            ]));

            $str = '?utm_source='.Yii::$app->request->get('utm_source').'&utm_medium='.Yii::$app->request->get('utm_medium').'&utm_ad='.Yii::$app->request->get('utm_ad').'&utm_content='.Yii::$app->request->get('utm_content').'&utm_design='.$utm_design.'&utm_google_pixel='.Yii::$app->request->get('utm_google_pixel').'&utm_facebook_pixel='.Yii::$app->request->get('utm_facebook_pixel').'&utm_yahoo_pixel='.Yii::$app->request->get('utm_yahoo_pixel');
        } elseif ($utm_one) {
            $str = '?utm_source='.$utm_one['utm_source'].'&utm_medium='.$utm_one['utm_medium'].'&utm_ad='.$utm_one['utm_ad'].'&utm_content='.$utm_one['utm_content'].'&utm_design='.$utm_one['utm_design'].'&utm_google_pixel='.$utm_one['utm_google_pixel'].'&utm_facebook_pixel='.$utm_one['utm_facebook_pixel'].'&utm_yahoo_pixel='.$utm_one['utm_yahoo_pixel'];
        } else {
            $str = '';
        }

        $server_name = $_SERVER['HTTP_HOST'];
        $pos = strpos($server_name, '.');
        $host = substr($server_name, 0, $pos);
        $domain = substr($server_name, $pos + 1);
        $model = new ProductRelease();
        $product = $model->find()->where(['domain' => $domain, 'host' => $host, 'is_del' => 0])->one();
        if (!$product) {
            return Error::errorJson(500);
        }

        $server_name = 'http://'.$server_name;

        //销量与百分比
        $sales_key = 'sales_'.$product->id;
        if (Yii::$app->cache->get($sales_key)) {
            Yii::$app->cache->set($sales_key, Yii::$app->cache->get($sales_key) + 1);
        } else {
            Yii::$app->cache->set($sales_key, rand(25000, 40000));
        }
        $sales_num = Yii::$app->cache->get($sales_key);
        $sales_max = 30000;
        if ($sales_num >= $sales_max) {
            $sales_max = $sales_num + 5000;
        }

        //产品详情替换img标签以作懒加载和video标签替换封面图
        if ($product->country == 'ID') {
            $product->pro_param = preg_replace('/<video(.*?)preload="none"/', '<video class="edui-upload-video vjs-default-skin video-js" controls preload="meta" poster="'.$server_name.'/themes/angeltmall/images/angeltmall.png"', $product->pro_param);
        } else {
            $product->pro_param = preg_replace('/<video(.*?)preload="none"/', '<video class="edui-upload-video vjs-default-skin video-js" controls preload="meta" poster="'.$server_name.'/themes/angeltmall/images/wowmall.png"', $product->pro_param);
        }
        $product->pro_param = preg_replace('/<img src="(.*?)"/', '<img class="lazy" src="'.$server_name.'/themes/angeltmall/images/loading.gif'.'" title=${1} alt="loading" style="width:100%"', $product->pro_param);

        //优惠

        //改变路由指向
        Yii::$app->getView()->theme = new Theme([
            'basePath' => '@app/themes/'.$product->theme_type,
            'pathMap' => [
                '@app/views' => '@app/themes/'.$product->theme_type,
            ],
        ]);

        return $this->renderPartial('/index');
    }

    /**
     * @fun 新增订单
     *
     * @author JW
     * @date 2018/06/02 ji
     */
    public function actionCreate()
    {
        // 实例化
        $sModel = new Order();
        $logMdel = new OrderLog();
        // 数据验证
        if ($sModel->load(Yii::$app->request->post(), '') && $sModel->validate()) {
            var_dump($sModel);die();
            $product = ProductRelease::find()->where(['id' => $sModel->productId])->one();

            $sModel->product = $sModel->name;
            $sModel->price = $product->sale_price;
            $sModel->payment_type = 'COD';
            $sModel->channel_type = $product->is_sensitive == 0 ? 'P' : 'M';
            $sModel->user_ip = $_SERVER['REMOTE_ADDR'];
            $sModel->create_time = time();
            $sModel->user_id = $product->create_by;
            $come_from = Yii::$app->request->cookies->getValue('from', false);
            $utm_date = unserialize(Yii::$app->request->cookies->getValue('utm', false));
            $sModel->utm_source = $utm_date['utm_source'];
            $sModel->utm_medium = $utm_date['utm_medium'];
            $sModel->utm_ad = $utm_date['utm_ad'];
            if (!$utm_date['utm_content']) {
                $AdminUser = new User();
                $utm_date['utm_content'] = $AdminUser->uidToAds[$product->ads_user];
            }
            $sModel->utm_content = $utm_date['utm_content'];
            //$sModel->utm_design = $utm_date['utm_design'];
            $sModel->come_from = $come_from;
            var_dump($sModel);die();
            switch ($sModel->country) {
                case 'ID':
                    $sModel->attributes = [
                        'status' => 10,
                        'comment' => '印尼地区目前不接受订单，自动取消',
                    ];
                    $sModel->save(false);
                    $logMdel->attributes = [
                        'oid' => $sModel->id,
                        'action_user' => '1',
                        'old_status' => '待确认',
                        'new_status' => '已取消',
                        'remark' => '印尼地区目前不接受订单，自动取消',
                        'action_ip' => $_SERVER['REMOTE_ADDR'],
                        'log_time' => time(),
                    ];
                    $logMdel->save();

                    return $this->jsonReturn('', '印尼地区目前不接受订单，自动取消');
                    break;
                default:
                    $memberModel = new Member();
                    $member = $memberModel->find()->where(['phone' => $sModel->phone])->one();
                    if ($member) {
                        if ($member->identity == 1) {
                            $sModel->attributes = [
                                'status' => 10,
                                'comment' => '黑名单用户，自动取消',
                            ];
                            $sModel->save(false);
                            $logMdel->attributes = [
                                'oid' => $sModel->id,
                                'action_user' => '1',
                                'old_status' => '待确认',
                                'new_status' => '已取消',
                                'remark' => '黑名单用户，自动取消',
                                'action_ip' => $_SERVER['REMOTE_ADDR'],
                                'log_time' => time(),
                            ];
                            $logMdel->save();

                            return $this->jsonReturn('', '黑名单用户，自动取消');
                        }
                        //老客户统计签收率
                        $userCount = $sModel->NumberCount($sModel->phone);
                        //判断是否有未签收订单
                        $statusCount = $sModel->StatusCount($sModel->phone);
                        //判断是否拒签
                        $RefuseCount = $sModel->RefuseCount($sModel->phone);
                        if ($statusCount) {
                            //存在未签收订单
                            //特殊地区
                            if ($sModel->country == 'teshudiqu') {
                                $sModel->attributes = [
                                    'status' => 10,
                                    'comment' => '特殊地区有未签收订单情况下均不接受新订单',
                                ];
                                $sModel->save(false);
                                $logMdel->attributes = [
                                    'oid' => $sModel->id,
                                    'action_user' => '1',
                                    'old_status' => '待确认',
                                    'new_status' => '已取消',
                                    'remark' => '特殊地区有未签收订单情况下均不接受新订单',
                                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                                    'log_time' => time(),
                                ];
                                $logMdel->save();

                                return $this->jsonReturn('', '特殊地区有未签收订单情况下均不接受新订单');
                            }

                            $data = Order::find()->where(['phone' => $sModel->phone])->andWhere(['not in', 'status', [9, 10, 11, 12, 13]])->all();
                            $skus = [];
                            foreach ($data as $v) {
                                $sku = OrderGoods::find()->select('sku_code')->where(['oid' => $v->id])->one();
                                if (!$sku) {
                                    continue;
                                }
                                $skus[] = $sku->sku_code;
                            }
                            $classify = [];
                            foreach ($skus as $v2) {
                                $classify[] = substr($v2, 0, 1);
                            }
                            $GoodsData = ProductRelease::find()->where(['id' => $sModel->website])->one();
                            $GoodsClassify = substr($GoodsData->spu_code, 0, 1);
                            if (in_array($GoodsClassify, $classify)) {
                                //存在同类产品
                                $sModel->attributes = [
                                    'status' => 10,
                                    'comment' => '同类产品不接受新订单',
                                ];
                                $sModel->save(false);
                                $logMdel->attributes = [
                                    'oid' => $sModel->id,
                                    'action_user' => '1',
                                    'old_status' => '待确认',
                                    'new_status' => '已取消',
                                    'remark' => '同类产品不接受新订单',
                                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                                    'log_time' => time(),
                                ];
                                $logMdel->save();

                                return $this->jsonReturn('', '同类产品不接受新订单');
                            } else {
                                //不存在，检查异类订单数量
                                if (count($classify) >= 4) {
                                    $sModel->attributes = [
                                        'status' => 10,
                                        'comment' => '同个用户异类产品最多只接收4个订单',
                                    ];
                                    $sModel->save(false);
                                    $logMdel->attributes = [
                                        'oid' => $sModel->id,
                                        'action_user' => '1',
                                        'old_status' => '待确认',
                                        'new_status' => '已取消',
                                        'remark' => '同个用户异类产品最多只接收4个订单',
                                        'action_ip' => $_SERVER['REMOTE_ADDR'],
                                        'log_time' => time(),
                                    ];
                                    $logMdel->save();

                                    return $this->jsonReturn('', '同个用户异类产品最多只接收4个订单');
                                }
                            }
                        } elseif ($RefuseCount) {
                            //存在拒签
                            if ($userCount < 0.6) {
                                $sModel->attributes = [
                                    'status' => 20,
                                    'comment' => '客服电话联系用户是否为恶意用户',
                                ];
                                $sModel->save(false);
                                $logMdel->attributes = [
                                    'oid' => $sModel->id,
                                    'action_user' => '1',
                                    'old_status' => '待确认',
                                    'new_status' => '待处理',
                                    'remark' => '客服电话联系用户是否为恶意用户',
                                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                                    'log_time' => time(),
                                ];
                                $logMdel->save();

                                return $this->jsonReturn('', '客服电话联系用户是否为恶意用户');
                            }

                            $unsign = Order::find()->where(['phone' => $sModel->phone])->limit(2)->orderBy('id DESC')->all();
                            $num = 0;//连续拒签的订单数量
                            $nums = 0;//订单订购的数量上限
                            $orderItem = [];//存放sku
                            foreach ($unsign as $v) {
                                if ($v->status == 13) {
                                    $num += 1;
                                }
                                $nums += $v->number;
                                $orderItem[] = OrderGoods::find()->select('sku_code')->where(['oid' => $v->id])->one()->sku_code;
                            }
                            if ($num == 2 && $nums == 12 && $orderItem[0] != $orderItem[1]) {
                                $sModel->attributes = [
                                    'status' => 10,
                                    'comment' => '同个用户存在两次拒签为异类，且数量均为产品购买上限',
                                ];
                                $sModel->save(false);
                                $logMdel->attributes = [
                                    'oid' => $sModel->id,
                                    'action_user' => '1',
                                    'old_status' => '待确认',
                                    'new_status' => '已取消',
                                    'remark' => '同个用户存在两次拒签为异类，且数量均为产品购买上限',
                                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                                    'log_time' => time(),
                                ];
                                $logMdel->save();

                                return $this->jsonReturn('', '同个用户存在两次拒签为异类，且数量均为产品购买上限');
                            }
                        }
                    } else {
                        //新客户
                        $memberModel->setIsNewRecord(true);
                        $memberModel->attributes = [
                            'name' => $sModel->user_name,
                            'phone' => $sModel->phone,
                            'county' => $sModel->country,
                            'phone_check' => '0',
                            'address' => $sModel->address,
                            'ip' => $_SERVER['REMOTE_ADDR'],
                            'create_time' => time(),
                            'identity' => '0',
                        ];
                        $memberModel->save();
                    }

            }

            if ($sModel->country == 'TH' || $sModel->country == 'MY') {
                $channelModel = new ChannelServices();
                $channel = $channelModel->checkPost($sModel->country, $sModel->post_code);
                if (!$channel) {
                    $sModel->attributes = [
                      'status' => 10,
                      'comment' => '订单不在配送范围',
                    ];
                    $sModel->save(false);
                    $logMdel->attributes = [
                        'oid' => $sModel->id,
                        'action_user' => '1',
                        'old_status' => '待确认',
                        'new_status' => '已取消',
                        'remark' => '订单不在配送范围',
                        'action_ip' => $_SERVER['REMOTE_ADDR'],
                        'log_time' => time(),
                    ];
                    $logMdel->save();

                    return $this->jsonReturn('', '订单不在配送范围');
                }
            }

            if ($sModel->number > 1 && $sModel->country == 'teshudiqu') {
                $sModel->attributes = [
                  'status' => 10,
                  'comment' => '特殊地区数量超过1,自动取消',
                ];
                $sModel->save(false);
                $logMdel->attributes = [
                    'oid' => $sModel->id,
                    'action_user' => '1',
                    'old_status' => '待确认',
                    'new_status' => '已取消',
                    'remark' => '特殊地区数量超过1,自动取消',
                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                    'log_time' => time(),
                ];
                $logMdel->save();

                return $this->jsonReturn('', '特殊地区数量超过1,自动取消');
            }

            if ($sModel->number > 6) {
                $sModel->attributes = [
                  'status' => 10,
                  'comment' => '数量超过6,自动取消',
                ];
                $sModel->save(false);
                $logMdel->attributes = [
                    'oid' => $sModel->id,
                    'action_user' => '1',
                    'old_status' => '待确认',
                    'new_status' => '已取消',
                    'remark' => '数量超过6,自动取消',
                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                    'log_time' => time(),
                ];
                $logMdel->save();

                return $this->jsonReturn('', '数量超过6,自动取消');
            }

            $res = $sModel->save(false);
            if ($res) {
                $itemModel = new OrderGoods();

                foreach ($buyGoods as $k => $v) {
                    $total = 0;
                    if ($v['gift'] != 1) {
                        $pd = Product::findOne($v['id']);
                        $next_price = ($pd->next_price >= 1) ? $pd->next_price : $pd->sale_price;
                        $total += $myPd->sale_price + $v['addPrice'] + ($v['qty'] - 1) * ($next_price + $v['addPrice']);
                    }

                    $product_attr = '产品ID:'.$pd->id.' ';

                    $skuReleaseRelation = SkuReleaseRelation::find()->where(['attr_one' => $v['attr'], 'p_r_id' => $_POST['p_r_id']])->one();
                    $sku = ProductSku::findOne($skuReleaseRelation->sku_id);
                    $sku_attribute = json_decode($sku->sku_attribute, true);
                    $size = $color = $image = '';
                    if ($sku_attribute['尺码'][0]) {
                        $product_attr .= '尺码: '.$sku_attribute['尺码'][0];
                        $size = $sku_attribute['尺码'][0];
                    }
                    if ($sku_attribute['颜色'][0]) {
                        $product_attr .= ' 颜色: '.$sku_attribute['颜色'][0];
                        $color = $sku_attribute['颜色'][0];
                        $image = isset($v['img']) ? $v['img'] : '';
                    }
                    if ($v['gift'] == 1) {
                        $product_attr = '【GIFT】'.$product_attr;
                    }

                    $combination = ProductDetails::find()->where(['sku' => $sku->sku_code])->one();
                    if ($combination->combination == 1) {
                        $combinations = Yii::$app->db->createCommand("select * from oms_combination where p_id = '{$combination->id}'")->queryAll();
                        foreach ($combinations as $ks => $vs) {
                            $itemModel->setIsNewRecord(true);
                            $product_combinations = Yii::$app->db->createCommand("select size as d_size,color as d_color,image as d_image from product_details where sku = '{$vs['sku']}'")->queryOne();
                            $product_attr = '产品ID:'.$product->id.' ';
                            $size = $color = $image = '';
                            if ($product_combinations['d_size']) {
                                $product_attr .= '尺寸: '.$product_combinations['d_size'];
                                $size = $product_combinations['d_size'];
                            }
                            if ($product_combinations['d_color']) {
                                $product_attr .= ' 颜色: '.$product_combinations['d_color'];
                                $color = $product_combinations['d_color'];
                                $image = $product_combinations['d_image'];
                            }
                            $itemModel->attributes = [
                                'order_id' => $sModel->id,
                                'product' => $product_attr,
                                'qty' => $v['qty'] * $vs['number'],
                                'price' => $total / count($combinations),
                                'size' => $size,
                                'color' => $color,
                                'image' => $image,
                                'sku' => $vs['sku'],
                            ];

                            unset($itemModel->id);
                            $itemModel->save();
                        }
                    } else {
                        $itemModel->setIsNewRecord(true);
                        $itemModel->attributes = [
                            'order_id' => $sModel->id,
                            'product' => $product_attr,
                            'qty' => $v['qty'],
                            'price' => $total,
                            'size' => $size,
                            'color' => $color,
                            'image' => $image,
                            'sku' => $sku->sku_code,
                        ];

                        unset($itemModel->id);
                        $itemModel->save();
                    }
                }
                //发送短信
                //$orderModel->sendMessage($sModel->id, $sModel->phone, $sModel->coutry);

                return $this->jsonReturn($sModel->id);
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
     * 下单成功页面接口.
     */
    public function actionSuccess()
    {
        $order_id = (int) Yii::$app->request->get('orderId');
        $product_id = (int) Yii::$app->request->get('productId');
        if (!$order_id || !$product_id) {
            return json_encode(['code' => 400, 'msg' => '失败']);
        }

        $product = Product::findOne($product_id);
        $order = order::findOne($order_id);
        //post back mv
        $order->postBackMv();
        //post back aff
        $order->postBackAff($order_id, $product->sku);
        //post back ob
        $order->postBackOb();
        //post back ta
        $order->postBackTa();

        return json_encode(['code' => 200, 'msg' => '成功']);
    }

    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @fun 更新订单
     *
     * @param id 产品分类id
     *
     * @author JW
     * @date 2018/06/02
     */
    public function actionUpdate()
    {
        // url上的id使用get方式获取
        $sModel = Order::find()->
        andWhere(['id' => Yii::$app->request->get('id'), 'is_del' => 0])->one();
        $a = Yii::$app->request->bodyParams;
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
     * @fun 软删除订单
     *
     * @param id 订单id
     *
     * @author JW
     * @date 2018/06/02
     */
    public function actionDelete()
    {
        // url上的id使用get方式获取
        $sModel = Order::find()->
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

    /*
     * 客服待确认订单列表
     */
    public function actionOrders()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询供应商数据
        $query = Order::find()->where(['is_del' => 0, 'status' => 1]);
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

    /*
     * 客服地区筛选列表
     */
    public function actionOrdersSearch($status, $county)
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        $query = Order::find()->with('OrderGoods')->where(['status' => $status, 'country' => $county, 'is_del' => 0]);
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

    /*
    * 客服待处理订单
    */
    public function actionHandles()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询待处理状态订单
        $query = Order::find()->where(['is_del' => 0, 'status' => 20]);
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
     * 待审订单列表检查sku.
     */
    public function actionCheckSku()
    {
        $id = Yii::$app->request->post('id');
        if ($res = Yii::$app->db->createCommand("select status from oms_order where id = '{$id}'")->queryOne()) {
            if ($res['status'] == 1 || $res['status'] == 15 || $res['status'] == 20) {
                if ($res = Yii::$app->db->createCommand("select sku_code from oms_orders_goods where oid = '{$id}'")->queryAll()) {
                    $err = 0;
                    foreach ($res as $v) {
                        if ($v['sku']) {
                            $err += 1;
                        }
                    }
                    if ($err == count($res)) {
                        return false;
                    } else {
                        return Error::errorJson(400, '该订单sku不全');
                    }
                } else {
                    return Error::errorJson(400, '该订单sku不全');
                }
            } else {
                return Error::errorJson(400, '该订单不是待确认或问题件或待处理，不能进行操作');
            }
        } else {
            return Error::errorJson(400, '该订单不存在');
        }
    }

    /**
     * 修改客服订单列表状态（取消）.
     *
     * @return mixed
     */
    public function actionOrdersQ()
    {
        $id = Yii::$app->request->post('id');
        $status_old = (Yii::$app->db->createCommand("select status from oms_order where id = '{$id}'")->queryOne())['status'];
        $status = Yii::$app->request->post('status');
        if ($status == 1) {
            $comment = '订单状态由[到货待确认]改为[缺货取消]';
            $order_status = '缺货取消';
        } else {
            $comment = '订单状态由[待确认]改为[已取消]';
            $order_status = '已取消';
        }
        if ($order_status == 19) {
            $cnstatus = '缺货取消';
        } elseif ($order_status == 10) {
            $cnstatus = '缺货取消';
        }
        if (Yii::$app->db->createCommand()->update('order', ['status' => 10], "id = {$id}")->execute()) {
            $logModel = new OrderLog();
            $logModel->attributes = [
                'oid' => $id,
                'status' => $cnstatus,
                'user_id' => Yii::$app->user->id,
                'comment' => $comment,
            ];
            $logModel->save();
            //回调AFF
            $this->postBackAff($id, 10);
            if ($status_old == 3) {
                $url = 'http://api.admin.com/purchases-detail/update-purchases-count';
                $this->http_request($id);
            }
            if ($status == 1) {
                return '状态:'.' '.'到货待确认'.' '.'=》'.' '.'缺货取消'.' '.'成功';
            } else {
                return '状态:'.' '.'待确认'.' '.'=》'.' '.'已取消'.' '.'成功';
            }
        } else {
            return Error::errorJson(400, '修改失败,请联系管理员');
        }
    }
    /*
     * 修改订单状态（问题件）
     * @return mixed
     */
    public function actionProblems()
    {
        if (Yii::$app->request->post()) {
            $order_id = Yii::$app->request->post('order_id');
            $problem = Yii::$app->request->post('problem');
            $status = Yii::$app->request->post('status');
            $description = Yii::$app->request->post('description');
            $track_number = Yii::$app->request->post('track_number');
            $time = date('Y-m-d H:i:s');
            if (!(Problems::find()->where(['order_id' => $order_id])->one())) {
                Yii::$app->db->createCommand()->insert('problems', ['order_id' => $order_id, 'problem' => $problem, 'status' => $status, 'description' => $description, 'track_number' => $track_number, 'create_date' => $time])->execute();
                $logModel = new OrderLog();
                $logModel->attributes = [
                    'order_id' => $order_id,
                    'status' => $status,
                    'user_id' => Yii::$app->user->id,
                    'comment' => '问题件表不存在该订单，写入该订单, 问题：'.$problem.'描述：'.$description,
                ];
                $logModel->save();
            } else {
                Yii::$app->db->createCommand()->update('problems', ['problem' => $problem, 'status' => $status, 'description' => $description, 'track_number' => $track_number, 'create_date' => $time], "order_id = {$order_id}")->execute();
                $logModel = new OrderLog();
                $logModel->attributes = [
                    'order_id' => $order_id,
                    'status' => $status,
                    'user_id' => Yii::$app->user->id,
                    'comment' => '问题件表存在该订单，更新该订单,问题：'.$problem.'描述：'.$description,
                ];
                $logModel->save();
            }
            Yii::$app->db->createCommand()->update('orders', ['status' => '问题件'], "id = {$order_id}")->execute();
            $logModel = new OrderLog();
            $logModel->attributes = [
                'order_id' => $order_id,
                'status' => '问题件',
                'user_id' => Yii::$app->user->id,
                'create_date' => date('Y-m-d'),
                'comment' => '订单表更新该订单为问题件',
            ];
            $logModel->save();

            return $this->jsonReturn();
        }

        return Error::errorJson(400, '修改失败,请联系管理员');
    }

    /**
     * 电话确认状态修改.
     */
    public function actionUpdateConfirm()
    {
        $id = Yii::$app->request->post('id');
        $confirm = Yii::$app->request->post('confirm');
        $model = $this->findModel($id);
        $logModel = new OrderLog();
        if ($model) {
            $comment = "电话确认由[{$model->confirm}] 修改为 [{$confirm}]";
            $model->confirm = $confirm;
            if ($model->save()) {
                $logModel->attributes = [
                    'order_id' => $id,
                    'status' => $model->status,
                    'comment' => $comment,
                    'user_id' => Yii::$app->user->id,
                ];
                $logModel->save();
                echo '状态修改成功！';
            } else {
                echo '状态修改失败！';
            }
        }
    }
    /**
     * 客服由已发货改为拒签
     * 更新物流状态
     */
    public function actionUpdateTrackStatusJq()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $model->status = '拒签';
        $logModel = new OrderLog();
        if ($model) {
            $comment = '客服由已发货改为拒签';
            if ($model->save()) {
                $logModel->attributes = [
                    'order_id' => $id,
                    'status' => $model->status,
                    'comment' => $comment,
                    'user_id' => Yii::$app->user->id,
                ];
                $logModel->save();

                return $this->jsonReturn();
            } else {
                return Error::errorJson(400, '状态修改失败');
            }
        }
    }

    /**
     * 添加备注.
     */
    public function actionAddComment()
    {
        $id = Yii::$app->request->post('id');
        $comment = Yii::$app->request->post('comment');
        $model = $this->findModel($id);
        $user = User::find()->where('id='.Yii::$app->user->id)->one();
        $user = $user->name;
        $logModel = new OrderLog();
        if ($model) {
            $model->comment_u = $model->comment_u.'<br>'.date('Y-m-d').' '.$user.':'.$comment;
            if ($model->save()) {
                $logModel->attributes = [
                    'order_id' => $id,
                    'status' => $model->status,
                    'comment' => '添加了备注：'.$model->comment_u,
                    'user_id' => Yii::$app->user->id,
                ];
                $logModel->save();

                return $this->jsonReturn();
            } else {
                return Error::errorJson(400, '添加备注失败');
            }
        }
    }

    public function actionCopyOrder()
    {
        if (Yii::$app->request->isPost) {
            $info = Yii::$app->request->post();
            $qty_count = 0;
            foreach ($info['qty'] as $qty) {
                if (empty($qty)) {
                    return Error::errorJson(400, '数量不能为空');
                }
                $qty_count += $qty;
            }
            $id = $info['Orders']['id'];
            $website = Order::findOne($id);
            $product_id = (ProductRelease::findOne($website->website))->id;

            if (Order::find()->where(['id' => $id])->one()) {
                Yii::$app->db->createCommand()->insert('order', [
                    'create_date' => time(),
                    'name' => $info['Orders']['name'],
                    'mobile' => $info['Orders']['mobile'],
                    'country' => $info['Orders']['county'],
                    'email' => $info['Orders']['email'],
                    'post_code' => $info['Orders']['post_code'],
                    'city' => $info['Orders']['city'],
                    'province' => $info['Orders']['province'],
                    'area' => $info['Orders']['area'],
                    'address' => $info['Orders']['address'],
                    'product' => $info['Orders']['product'],
                    'qty' => $qty_count,
                    'price' => $info['cost'],
                    'pay' => $website->pay,
                    'status' => 1,
                    'comment_u' => '从'.$id.'订单生成新订单',
                    'website' => $website->website,
                    'comment' => $website->comment,
                    'shipping_time' => $website->shipping_time,
                    'ip' => $website->ip,
                    'confirm' => $website->confirm,
                    'ip_a' => $website->ip_a,
                    'mobile_check' => $website->mobile_check,
                    'is_picked' => $website->is_picked,
                    'utm_design' => $website->utm_design,
                    'utm_content' => $website->utm_content,
                    'utm_ad' => $website->utm_ad,
                    'utm_medium' => $website->utm_medium,
                    'utm_source' => $website->utm_source,
                    'update_time' => $website->update_time,
                    'back_date' => $website->back_date,
                    'other_fee' => $website->other_fee,
                    'shipping_fee' => $website->shipping_fee,
                    'back_total' => $website->back_total,
                    'denial_of_time' => $website->denial_of_time,
                    'purchase_time' => $website->purchase_time,
                    'confirm_time' => $website->confirm_time,
                    'df_tracknumber' => $website->df_tracknumber,
                    'channel_type' => $website->channel_type,
                    'cost' => $website->cost,
                    'pickup_date' => $website->pickup_date,
                    'track_status' => $website->track_status,
                    'delivery_date' => $website->delivery_date,
                    'dd_fail' => $website->dd_fail,
                    'address_error' => $website->address_error,
                    'cod_fee' => $website->cod_fee,
                    'lc' => $website->lc,
                    'district' => $website->district,
                    'id_card' => $website->id_card,
                    'copy_admin' => Yii::$app->user->id,
                ])->execute();
                $orderid = Yii::$app->db->getLastInsertID();

                $logModel = new OrderLog();
                $logModel->attributes = [
                    'oid' => $orderid,
                    'action_user' => '1',
                    'old_status' => $website->status,
                    'new_status' => 1,
                    'remark' => '修改订单',
                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                    'log_time' => time(),
                ];
                $logModel->save();

                if ($info['sku']) {
                    for ($i = 0; $i < count($info['sku']); ++$i) {
                        $product = '产品ID：'.$info['tc_id'][$i].' 尺寸：'.$info['size'][$i].' 颜色：'.$info['color'][$i];
                        $attr_color = [
                            'color' => $info['color'][$i],
                            'size' => $info['size'][$i],
                            'img' => $info['img'][$i],
                        ];
                        Yii::$app->db->createCommand()->insert('oms_order_goods', array(
                            'oid' => $id,
                            'p_r_id' => $website->website,
                            'is_del' => 0,
                            'update_by' => Yii::$app->user->id,
                            'create_time' => time(),
                            'product' => $product,
                            'sku_code' => $info['sku_code'][$i],
                            'number' => $info['number'][$i],
                            'price' => $info['price'][$i],
                            'size' => $info['size'][$i],
                            'color' => $info['color'][$i],
                            'img' => $info['img'][$i],
                            'attr_json' => json_encode($attr_color),
                        ))->execute();
                        $logModel = new OrderLog();
                        $logModel->attributes = [
                            'oid' => $id,
                            'action_user' => '1',
                            'old_status' => $website->status,
                            'new_status' => 1,
                            'remark' => '待审列表修改订单写orders-item表,qty为'.$info['qty'][$i].'price为'.$info['price'][$i].'size为'.$info['size'][$i].'color为'.$info['color'][$i],
                            'action_ip' => $_SERVER['REMOTE_ADDR'],
                            'log_time' => time(),

                        ];
                        $logModel->save();
                    }

                    return $this->jsonReturn();
                }
            } else {
                return Error::errorJson(400, '订单未取消');
            }
        } else {
            $id = Yii::$app->request->get('id');
            $model = $this->findModel($id);
            $products = ProductSku::find()->where(['p_id' => $model->website])->all();
            $size = $color = [];
            foreach ($products as $product) {
                if (!(in_array($product->color, $color))) {
                    $color[] = $product->color;
                }
                if (!(in_array($product->size, $size))) {
                    $size[] = $product->size;
                }
            }

            $info = [$color, $size, $products, $id];
            if (!empty($color) || !empty($size)) {
                for ($i = 0; $i < count($color); ++$i) {
                    if (is_null($model->items[$i]->sku)) {
                        if ((ProductSku::find()->where(['color' => $color[$i], 'size' => $size[$i], 'p_id' => $model->website])->one())->sku) {
                            $item_id = $model->items[$i]->id;
                            Yii::$app->db->createCommand()->update('orders_item', ['sku' => (ProductSku::find()->where(['color' => $color[$i], 'size' => $size[$i], 'p_id' => $model->website])->one())->sku,
                            ], "id = '$item_id'")->execute();
                            $logModel = new OrderLog();
                            $logModel->attributes = [
                                'order_id' => $id,
                                'status' => (Order::find()->where(['id' => $id])->one())->status,
                                'user_id' => Yii::$app->user->id,
                                'create_date' => date('Y-m-d'),
                                'comment' => '更新orders_item sku',
                            ];
                            $logModel->save();
                        }
                    }
                }
            }

            $model = $this->findModel($id);

            return $this->render('copy-order', [
                'model' => $model,
                'info' => $info,
            ]);
        }
    }

    /*
  * ajax提交修改状态
  */
    public function actionAjaxStatus()
    {
        $id = Yii::$app->request->post('id');
        $comment = Yii::$app->request->post('comment');
        $model = $this->findModel(['id' => $id]);
        $model->comment_u = $comment;
        $model->confirm_time = date('Y-m-d H:i:s');
        if ($model->save()) {
            $logModel = new OrderLog();
            $logModel->attributes = [
                'order_id' => $id,
                'status' => $model->status,
                'user_id' => Yii::$app->user->id,
                'create_date' => date('Y-m-d h:i:s'),
                'comment' => '生成了新订单!',
            ];
            $logModel->save();

            return $this->jsonReturn();
        } else {
            return Error::errorJson(400, '');
        }
    }

    /*
    * 判断是否能够转运
    */
    /**
     * @throws \yii\db\Exception
     */
    public function actionUpdateStock()
    {
        $id = Yii::$app->request->post('id');
        $OrdersData = Orders::findOne($id);
        $items = Yii::$app->db->createCommand("SELECT qty, sku FROM orders_item WHERE order_id='{$id}' ORDER BY sku ASC")->queryAll();
        $res2 = array();
        $item_md5 = '';
        foreach ($items as $item) {
            $item_md5 .= $item['sku'].'='.$item['qty'].'&';
        }
        $item_md5 = md5($item_md5);
        if (empty($OrdersData->county) || $OrdersData->county == '香港') {
            $OrdersData->county = 'HK';
        }
        if ($OrdersData->county == '臺灣') {
            $OrdersData->county = 'TW';
        }

        $forwarding = Yii::$app->db->createCommand('SELECT `code` FROM forwarding_company WHERE transport_state=0')->queryColumn();
        $fw_code = implode("','", $forwarding);

        $sql = "SELECT B.qty, B.sku, B.order_id, A.id FROM stocks AS A LEFT JOIN orders_item AS B ON A.order_id=B.order_id LEFT JOIN orders AS C ON A.order_id=C.id WHERE A.county='{$OrdersData->county}' AND A.status=0 AND C.lc NOT IN ('{$fw_code}') ORDER BY B.sku ASC";

        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $stocks = array();
        foreach ($data as $item) {
            $stocks[$item['order_id']][] = ['sku' => $item['sku'], 'qty' => $item['qty'], 'id' => $item['id']];
        }
        $data2 = '';
        foreach ($stocks as $stock) {
            $md5 = '';
            foreach ($stock as $v) {
                $md5 .= $v['sku'].'='.$v['qty'].'&';
            }
            $md5 = md5($md5);
            if ($item_md5 == $md5) {
                $data2 = $stock[0];
                break;
            }
        }

        if ($data2) {
            echo '存在转运产品';
        } else {
            echo '不存在转运产品';
        }
    }

    /**
     * AFF回调.
     *
     * @param $order_id
     */
    protected function postBackAff($order_id, $status)
    {
        $url = 'http://www.midastouch.cc/orders/update-status?order_id='.urlencode($order_id).'&status='.urlencode($status);
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_exec($ch);
//            $file_contents = curl_exec($ch);
//            file_put_contents('./aff.txt', $file_contents);
        curl_close($ch);

        return true;
    }

    /**
     * 客服取消订单  修改  form   number 减数量  record_form_out   删除记录.
     */
    public function descForm($id)
    {
        //        echo '接受的订单id'.$id.' ';
        if ($res = Yii::$app->db->createCommand("select id from record_from where o_id = '{$id}'")->queryOne()) {
            //            echo 'record_from查询到记录 ';
            if ($order = Yii::$app->db->createCommand("select * from orders_item where order_id = '{$id}'")->queryAll()) {
                //                echo 'orders_item查询到记录 ';
                foreach ($order as $k => $v) {
                    $r_id = Yii::$app->db->createCommand("select f.id from form as f LEFT JOIN record_from as r on r.f_id = f.id where r.o_id = '{$id}' and f.sku = '{$v['sku']}'")->queryOne();
                    $r_id = $r_id['id'];
                    if (Yii::$app->db->createCommand("update form set number = number-'{$v['qty']}' where id = '{$r_id}'")->execute()) {
                        //                        echo 'form表中sku为'.$v['sku'].'订单id为'.$id.'的记录数量减'.$v['qty'].' ';
                        if ($form_number = Yii::$app->db->createCommand("select f.* from form as f LEFT JOIN record_from as r on r.f_id = f.id where r.o_id = '{$id}' and f.sku = '{$v['sku']}'")->queryOne()) {
                            //                            echo 'form找到记录 ';
                            if ($form_number['number'] == 0) {
                                //                                echo 'form表中数量为0 ';
                                if (Yii::$app->db->createCommand()->update('form', ['status' => 4], "id = '{$form_number['id']}'")->execute()) {
                                    //                                    echo '更新form表中id为'.$form_number['id'].'的记录状态为已取消  ';
                                } else {
                                    //                                    echo '更新form表中id为'.$form_number['id'].'的记录失败  ';
                                }
                            } else {
                                //                                echo 'form表中数量不为0 ';
                            }
                        } else {
                            //                            echo 'form找不到记录 ';
                        }
                    } else {
                        //                        echo '更新form表失败 ';
                    }
                }
                if (Yii::$app->db->createCommand()->update('record_from', ['cancel' => 1], "o_id = '{$id}'")->execute()) {
                    //                    echo '伪删除record_from中订单id为'.$id.'的记录成功 ';
                } else {
                    //                    echo '删除record_from中订单id为'.$id.'的记录失败 ';
                }
            } else {
                //                echo 'orders_item查询不到记录 ';
            }
        } else {
            //            echo 'record_from查询不到记录 ';
        }
    }

    /**
     * @fun 采购待确认订单
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author JW
     * @date 2018/06/02
     */
    public function actionPurchaseIndex()
    {
        // 接收参数
        $offset = Yii::$app->request->get('offset', 0);
        $length = Yii::$app->request->get('length', 10);
        // 查询供应商数据
        $query = Order::find()->where(['is_del' => 0])->andWhere(['status' => 20]);
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

    public function http_request($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);

        return $output;
    }

    /**
     * @fun 订单搜索
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author JW
     * @date 2018/06/02
     */
    public function actionSearch()
    {
        $query = Order::find()->select('id,total_price,website,oms_order.country,user_name,phone,user_ip,comment,oms_order.create_time')->where(['oms_order.is_del' => 0]);
        $id = Yii::$app->request->get('id');
        $name = Yii::$app->request->get('name');
        $mobile = Yii::$app->request->get('mobile');
        $country = Yii::$app->request->get('country');
        $product = Yii::$app->request->get('product');
        $email = Yii::$app->request->get('email');
        $spu = Yii::$app->request->get('spu');
        $track = Yii::$app->request->get('track');
        $type = Yii::$app->request->get('type');
        $date1 = Yii::$app->request->get('date1');
        $date2 = Yii::$app->request->get('date2');
        $date3 = Yii::$app->request->get('date3');
        $date4 = Yii::$app->request->get('date4');
        if ($spu) {
            $query->joinWith('productRelease');
            $query->andFilterWhere(['spu_code' => $spu]);
        }
        if ($id) {
            $query->andFilterWhere(['oms_order.id' => $id]);
        }
        if ($name) {
            $query->andFilterWhere(['like', 'user_name', $name]);
        }
        if ($mobile) {
            $query->andFilterWhere(['phone' => $mobile]);
        }
        if ($country) {
            $query->andFilterWhere(['oms_order.country' => $country]);
        }
        if ($product) {
            $query->andFilterWhere(['like', 'product', $product]);
        }
        if ($email) {
            $query->andFilterWhere(['email' => $email]);
        }
        if ($type) {
            foreach ($type as $k => $v) {
                switch ($v) {
                    case '待确认':
                        $type[$k] = 1;
                        break;
                    case '已确认':
                        $type[$k] = 2;
                        break;
                    case '待发货':
                        $type[$k] = 3;
                        break;
                    case '已发货':
                        $type[$k] = 4;
                        break;
                    default:
                        $type[$k] = 0;
                        break;
                }
            }
            $query->andFilterWhere(['in', 'status', $type]);
        }

        $time1 = $date1 ? strtotime($date1) : strtotime('2017-1-1');
        $time2 = $date2 ? strtotime($date2) : time();
        // $time3 = $date3 ? strtotime($date3) : '';
        // $time4 = $date4 ? strtotime($date4) : '';
        $query->andFilterWhere(['between', 'create_time', $time1, $time2]);

        $res = $query->asArray()->all();
        foreach ($res as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }

        return $this->jsonReturn($res);
    }

    /**
     * @fun 订单详情
     *
     * @param offset 起始页
     * @param length 查询页数
     *
     * @author JW
     * @date 2018/06/02
     */
    public function actionDetail()
    {
        $id = Yii::$app->request->get('id');
        $model = new Order();
        $order = $model->find()->where(['id' => $id])->asArray()->one();
        $orderGoods = OrderGoods::find()->select('id,attr_json,sku_code,pname,p_r_id,number,price')->where(['oid' => $id])->asArray()->all();
        $orderLogs = OrderLog::find()->where(['oid' => $id])->asArray()->all();
        $trackLogs = TrackLog::find()->where(['order_id' => $id])->asArray()->all();
        $user = User::find()->select('id,username')->asArray()->all();
        $order['create_time'] = date('Y-m-d H:i:s', $order['create_time']);
        $order['status'] = $model->intStatus[$order['status']];

        foreach ($orderLogs as &$v) {
            $v['log_time'] = date('Y-m-d H:i:s', $v['log_time']);
            $v['old_status'] = $model->intStatus[$v['old_status']];
            $v['new_status'] = $model->intStatus[$v['new_status']];
            foreach ($user as $vs) {
                if ($v['action_user'] == $vs['id']) {
                    $v['action_user'] = $vs['username'];
                }
            }
        }

        foreach ($orderGoods as &$v) {
            $v['attr_json'] = json_decode($v['attr_json'], true);
            $v['img'] = $v['attr_json']['img'];
            unset($v['attr_json']['img']);
            foreach ($v['attr_json'] as $ks => $vs) {
                $v['attr_one'][] = [
                                        'attr' => $ks,
                                        'value' => $vs,
                                    ];
            }
            unset($v['attr_json']);
            $pr = ProductRelease::find()->select('attr_list')->where(['id' => $v['p_r_id']])->asArray()->one()['attr_list'];
            foreach (json_decode($pr, true) as $kss => $vss) {
                $v['attr_list'][] = [
                                        'attr' => $kss,
                                        'value' => $vss,
                                    ];
            }
        }

        foreach ($trackLogs as &$v) {
            $v['track_time'] = date('Y-m-d H:i:s', $v['track_time']);
        }

        $detail = [];
        $detail = [
                    'order' => $order,
                    'orderGoods' => $orderGoods,
                    'orderLogs' => $orderLogs,
                    'trackLogs' => $trackLogs,
                  ];

        return $this->jsonReturn($detail);
    }

    /**
     * 修改订单状态
     */
    public function actionUpdateStatus()
    {
        if (in_array(Yii::$app->user->id, [1, 2, null])) {
            $order_id = Yii::$app->request->post('order_id');
            $status = Yii::$app->request->post('status');
            $remark = Yii::$app->request->post('remark', '客服修改状态');

            $model = $this->findModel($order_id);
            //状态相同不可修改
            if ($model->status == $status) {
                return $this->jsonReturn('', '状态相同不可修改');
            }
            //已取消订单不可逆
            if ($model->status == 10 || $model->status == 9 || $model->status == 13) {
                return $this->jsonReturn('', '已取消或已签收或拒签订单不可修改');
            }
            //已确认订单不可变为待确认
            if ($model->status == 2 && $status == 1) {
                return $this->jsonReturn('', '已确认订单不可变为待确认');
            }

            if ($status == 20) {
                $type = Yii::$app->request->post('type');
                $track_number = Yii::$app->request->post('track_number');
                $sonStatus = Yii::$app->request->post('sonStatus');
                $pModel = new Problems();
                $pModel->attributes = [
                    'order_id' => $order_id,
                    'problem' => $type,
                    'status' => $sonStatus,
                    'create_time' => time(),
                    'description' => $remark,
                    'track_number' => $track_number,
                ];
                $pModel->save();
            }
            if ($status == 2) {
                $model->status = $status;
                $orderTimeModel = new OrderTime();
                $orderTimeModel::find()->where(['oid' => $order_id])->one();
                $orderTimeModel->confirm_time = date('Y-m-d H:i:s');
                $orderTimeModel->save();
                //调整发货渠道
                $model->updateTrackName();
                //回调AFF
                $this->postBackAff($order_id, '已确认');
            }

            $logModel = new OrderLog();
            $logModel->attributes = [
                'oid' => $order_id,
                'action_user' => '1',
                'old_status' => $model->status,
                'new_status' => $status,
                'remark' => $remark,
                'action_ip' => $_SERVER['REMOTE_ADDR'],
                'log_time' => time(),
            ];
            $logModel->save();
            $model->status = $status;
            if ($model->save()) {
                return $this->jsonReturn();
            } else {
                return Error::errorJson(400, '状态修改失败');
            }
        } else {
            return Error::errorJson(400, '没有权限');
        }
    }

    /**
     * 待确认和问题单.
     */
    public function actionAuditOrder()
    {
        $page = Yii::$app->request->get('page', 1);
        $pageSize = Yii::$app->request->get('pageSize', 10);
        $status = Yii::$app->request->get('status');
        $country = Yii::$app->request->get('country');
        if ($page < 1) {
            return Error::errorJson(400, '页数不能小于1');
        }
        if (!$status) {
            $status = [1, 20];
        }
        $offset = ($page - 1) * $pageSize;
        $model = new Order();
        $query = $model->find()
                       ->select('id,total_price,website,country,user_name,phone,user_ip,comment,create_time,status')
                       ->where(['is_del' => 0])
                       ->andFilterWhere(['in', 'status', $status])
                       ->orderBy('status ASC,id ASC');

        if ($country) {
            $query->andFilterWhere(['country' => $country]);
        }
        $all = $query->count();
        $res = $query->offset($offset)->limit($pageSize)->asArray()->all();
        $data['list'] = [];
        foreach ($res as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['status'] = $model->intStatus[$v['status']];
            $data['list'][] = $v;
        }
        $data['totalSize'] = $all;

        return $this->jsonReturn($data);
    }

    public function actionUpdateProductOrders()
    {
        $id = Yii::$app->request->post('id');
        $pr = ProductRelease::find()->select('attr_list,')->where(['id' => $id])->asArray()->one()['attr_list'];
        foreach (json_decode($pr, true) as $kss => $vss) {
            $v['attr_list'][] = [
                'attr' => $kss,
                'value' => $vss,
            ];
        }

        return $this->jsonReturn($v);
    }

    /**
     * 修改订单.
     */
    public function actionUpdateOrder()
    {
        if (Yii::$app->request->isPost) {
            $info = Yii::$app->request->post();

            $qty_count = 0;
            foreach ($info['Orders']['orderGoods'] as $qty) {
                if (empty($qty['number'])) {
                    return Error::errorJson(400, '购买量不能为空');
                }
                $qty_count += $qty['number'];
            }
            $sku = [];
            foreach ($info['Orders']['orderGoods'] as $v3) {
                if (empty($v3['sku_code'])) {
                    return Error::errorJson(400, 'sku不能为空');
                }
                $sku[] = $v3['sku_code'];
            }

            $id = $info['Orders']['id'];
            $website = Order::findOne($id);
            $product_id = (ProductRelease::findOne($website->website))->id;
            $OrderData = Order::find()->where(['id' => $id])->one();
            if ($OrderData) {
                Yii::$app->db->createCommand()->update('oms_order', ['id' => $id,
                    'create_time' => time(),
                    'phone' => $info['Orders']['mobile'],
                    'country' => $info['Orders']['country'],
                    'email' => $info['Orders']['email'],
                    'post_code' => $info['Orders']['post_code'],
                    'city' => $info['Orders']['city'],
                    'province' => $info['Orders']['province'],
                    'area' => $info['Orders']['area'],
                    'address' => $info['Orders']['address'],
                    'comment' => $info['Orders']['comment'],
                    'product' => $info['Orders']['product'],
                    'number' => $qty_count,
                    'total_price' => $info['Orders']['total_price'],

                ], "id = '$id'")->execute();

                $logModel = new OrderLog();
                $logModel->attributes = [
                    'oid' => $id,
                    'action_user' => '1',
                    'old_status' => $OrderData->status,
                    'new_status' => $OrderData->status,
                    'remark' => '修改订单',
                    'action_ip' => $_SERVER['REMOTE_ADDR'],
                    'log_time' => time(),
                ];
                $logModel->save();

                if ($sku) {
                    Yii::$app->db->createCommand()->delete('oms_order_goods', "oid = '$id'")->execute();
                    foreach ($info['Orders']['orderGoods'] as $gooddata) {
                        $size = $gooddata['attr_one'][1]['value'];
                        $color = $gooddata['attr_one'][0]['value'];
                        $release = Yii::$app->db->createCommand("select * from oms_sku_release_relation WHERE sku_code = '{$gooddata['sku_code']}' AND promotion_type = 1")->queryOne();

                        if ($release) {
                            $goodsModel = new OrderGoods();
                            $combinations = Yii::$app->db->createCommand("select * from oms_promotion WHERE s_r_id = '{$release['id']}' AND type = 1")->queryAll();
                            foreach ($combinations as $k => $v) {
                                $goodsModel->setIsNewRecord(true);
                                $skuData = Yii::$app->db->createCommand("select * from oms_sku_release_relation WHERE sku_id = '{$v['sku_id']}' ")->queryOne();
                                $attr = json_decode($skuData['attr_one'], true);

                                $product_attr = '产品ID:'.$product_id.' ';
                                $size = $color = $image = '';
                                if ($size) {
                                    $product_attr .= '尺寸: '.$size;
                                }
                                if ($color) {
                                    $product_attr .= ' 颜色: '.$color;

                                    $image = $attr['img'];
                                }
                                $attr_color = [
                                    'color' => $attr[0],
                                    'size' => $attr[1],
                                    'img' => $image,
                                ];
                                $goodsModel->attributes = [
                                    'order_id' => $id,
                                    'pname' => $product_attr,
                                    'number' => $info['number'],
                                    'price' => $info['price'],
                                    'size' => $attr[1],
                                    'color' => $attr[0],
                                    'img' => $image,
                                    'sku_code' => $v['sku_code'],
                                    'update_by' => 1,
                                    'is_del' => 0,
                                    'attr_json' => json_encode($attr_color),
                                ];
                                unset($goodsModel->id);
                                $goodsModel->save();
                            }
                        } else {
                            $product = '产品ID：'.$product_id.' 尺寸：'.$size.' 颜色：'.$color;
                            $attr_color = [
                                'color' => $color,
                                'size' => $size,
                                'img' => $gooddata['img'],
                            ];

                            Yii::$app->db->createCommand()->insert('oms_order_goods', array(
                                'oid' => $id,
                                'p_r_id' => $product_id,
                                'is_del' => 0,
                                'update_by' => 1,
                                'create_time' => time(),
                                'product' => $product,
                                'sku_code' => $gooddata['sku_code'],
                                'number' => $gooddata['number'],
                                'price' => $gooddata['price'],
                                'size' => $size,
                                'color' => $color,
                                'img' => $gooddata['img'],
                                'attr_json' => json_encode($attr_color),
                            ))->execute();
                            $logModel = new OrderLog();
                            $logModel->attributes = [
                                'oid' => $id,
                                'action_user' => '1',
                                'old_status' => $OrderData->status,
                                'new_status' => $OrderData->status,
                                'remark' => '待审列表修改订单写orders-goods表,qty为'.$gooddata['number'].'price为'.$gooddata['price'].'size为'.$size.'color为'.$color,
                                'action_ip' => $_SERVER['REMOTE_ADDR'],
                                'log_time' => time(),

                            ];
                            $logModel->save();
                        }
                    }

                    return $this->jsonReturn();
                }
            } else {
                return Error::errorJson(400, '找不到订单ID');
            }
        } else {
            return Error::errorJson(404);
        }
    }

    /**
     * 修改属性.
     */
    public function actionOrderAttr()
    {
        $p_r_id = Yii::$app->request->post('p_r_id');
        $attr = Yii::$app->request->post('attr_one');
        if (!$p_r_id) {
            return Error::errorJson(400, '缺少p_r_id');
        }

        $attr2 = [];
        foreach ($attr as $v) {
            $attr2[$v['attr']] = $v['value'];
        }
        $attr2 = json_encode($attr2);
        $res = Yii::$app->db->createCommand("SELECT img,price,sku_code FROM oms_sku_release_relation WHERE attr_one='{$attr2}' AND p_r_id='{$p_r_id}'")->queryOne();

        return $this->jsonReturn($res);
    }
}
