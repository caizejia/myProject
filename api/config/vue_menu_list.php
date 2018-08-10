<?php

return [
    [
        'icon' => 'icon-hammer',
        'title' => '任务',
        'index' => 'taskManage',
        'subs' => [
            [
                'title' => '需求提交',
                'subs' => [
                    [
                        'title' => '需求列表',
                        'router' => '/taskManage/submitDemand',
                    ],
                ],
            ],
            [
                'title' => '审核',
                'subs' => [
                    [
                        'title' => '翻译审核',
                        'router' => '/taskManage/check',
                    ],
                ],
            ],
            [
                'title' => '任务列表',
                'subs' => [
                    [
                        'title' => '翻译任务',
                        'router' => '/taskManage/translateTask',
                    ],
                    [
                        'title' => '设计任务',
                        'router' => '/taskManage/designTask',
                    ],
                ],
            ],
        ],
    ],
    [
        'icon' => 'el-icon-document',
        'index' => 'order',
        'title' => '订单',
        'subs' => [
            [
                'title' => '订单管理',
                'subs' => [
                    [
                        'title' => '订单管理',
                        'router' => '/order/index',
                    ],
                    [
                        'title' => 'FB专用列表',
                        'router' => '/order/index',
                    ],
                ],
            ],
            [
                'title' => '订单审核',
                'subs' => [
                    [
                        'title' => '订单审核',
                        'router' => '/order/orderAudit',
                    ],
                ],
            ],
            [
                'title' => '退单统计',
                'subs' => [
                    [
                        'title' => '退单统计',
                        'router' => '/order/index',
                    ],
                ],
            ],
        ],
    ],
    [
        'icon' => 'el-icon-goods',
        'index' => 'product',
        'title' => '产品',
        'subs' => [
            [
                'title' => '产品管理',
                'subs' => [
                    [
                        'title' => '产品中心',
                        'router' => '/product/check/index',
                    ],
                    [
                        'title' => '产品列表',
                        'router' => '/product/index',
                    ],
                    [
                        'title' => '发布产品列表',
                        'router' => '/product/release/index',
                    ],
                ],
            ],
            [
                'title' => '站点管理',
                'subs' => [
                    [
                        'title' => '站点列表',
                        'router' => '#',
                    ],
                    [
                        'title' => '广告运营列表',
                        'router' => '#',
                    ],
                ],
            ],
            [
                'title' => '产品相关',
                'subs' => [
                    [
                        'title' => '分类列表',
                        'router' => '/product/category/index',
                    ],
                    [
                        'title' => '供应商列表',
                        'router' => '/product/supplier/index',
                    ],
                    [
                        'title' => '品牌列表',
                        'router' => '/product/brand/index',
                    ],
                    [
                        'title' => '属性列表',
                        'router' => '/product/attr/index',
                    ],
                ],
            ],
        ],
    ],
    [
        'icon' => 'icon-coin-dollar',
        'index' => 'finance',
        'title' => '财务',
        'subs' => [
            [
                'title' => '账单列表',
                'subs' => [
                    [
                        'title' => '导入账单',
                        'router' => '/finance/upload',
                    ],
                    [
                        'title' => '核对账单',
                        'router' => '/product/add',
                    ],
                    [
                        'title' => '导出账单',
                        'router' => '/product/check/add',
                    ],
                ],
            ],
            [
                'title' => '应收',
                'subs' => [
                    [
                        'title' => 'COD收款单',
                        'router' => '/product/index',
                    ],
                    [
                        'title' => '赔偿收款单',
                        'router' => '/product/add',
                    ],
                ],
            ],
            [
                'title' => '付款单',
                'subs' => [
                    [
                        'title' => 'COD手续费',
                        'router' => '/product/index',
                    ],
                    [
                        'title' => '头程费用',
                        'router' => '/product/add',
                    ],
                    [
                        'title' => '尾程费用',
                        'router' => '/product/add',
                    ],
                    [
                        'title' => '销售退款',
                        'router' => '/product/add',
                    ],
                ],
            ],
            [
                'title' => '费用计算',
                'subs' => [
                    [
                        'title' => '运费计算',
                        'router' => '/product/index',
                    ],
                    [
                        'title' => 'COD手续费计算',
                        'router' => '/product/add',
                    ],
                    [
                        'title' => '退款手续费计算',
                        'router' => '/product/add',
                    ],
                    [
                        'title' => '其它费用计算',
                        'router' => '/product/add',
                    ],
                ],
            ],
            [
                'title' => '导入',
                'subs' => [
                    [
                        'title' => '运费计算',
                        'router' => '/product/index',
                    ],
                    [
                        'title' => 'COD手续费计算',
                        'router' => '/product/add',
                    ],
                    [
                        'title' => '退款手续费计算',
                        'router' => '/product/add',
                    ],
                    [
                        'title' => '其它费用计算',
                        'router' => '/product/add',
                    ],
                ],
            ],
        ],
    ],
    [
        'icon' => 'el-icon-tickets',
        'index' => 'report',
        'title' => '报表',
        'subs' => '',
    ],
    [
        'icon' => 'icon-cart',
        'index' => 'caigou',
        'title' => '采购',
        'subs' => [
            [
                'title' => '采购管理',
                'subs' => [
                    [
                        'title' => '采购单列表',
                        'router' => '/purchases/index',
                    ],
                    [
                        'title' => '采购单汇总明细',
                        'router' => '/purchases-detail/index',
                    ],
                    [
                        'title' => '采购付款单',
                        'router' => '/payments/index',
                    ],
                    [
                        'title' => '采购收货单',
                        'router' => '/pw-bill/index',
                    ],
                    [
                        'title' => '收货异常处理',
                        'router' => '/product/release/add',
                    ],

                ],
            ],
            [
                'title' => '采购异常',
                'subs' => [
                    [
                        'title' => '退货单列表',
                        'router' => '/pr-bill/index',
                    ],
                    [
                        'title' => '退款单列表',
                        'router' => '/receivables/index',
                    ],

                ],
            ],
        ],
    ],

    [
        'icon' => 'icon-truck',
        'index' => 'pick',
        'title' => '发货',
        'subs' => [
            [
                'title' => '发货管理',
                'subs' => [
                    [
                        'title' => '拣货波次单',
                        'router' => '/pick-round/index',
                    ],
                    [
                        'title' => '打包复核',
                        'router' => '/pick-round/check',
                    ],
                    [
                        'title' => '包裹尺寸录入',
                        'router' => '/pick-round/size',
                    ],
                    [
                        'title' => '称重',
                        'router' => '/pick-round/weight',
                    ],
                    [
                        'title' => '出库',
                        'router' => '/pick-round/out',
                    ],
                    [
                        'title' => '异常单处理',
                        'router' => '/pick-round/problems',
                    ],
                ],
            ],
        ],
    ],

    [
        'icon' => 'icon-office',
        'index' => 'cangku',
        'title' => '仓库管理',
        'subs' => [
            [
                'title' => '自建仓管理',
                'subs' => [
                    [
                        'title' => '建仓管理',
                        'router' => '/warehouse/index',
                    ],
                    [
                        'title' => '商品仓库库存情况',
                        'router' => '/inventory/index',
                    ],
                    [
                        'title' => '库位库区管理',
                        'router' => '/sub-inventory/index',
                    ],
                    [
                        'title' => '采购入库管理',
                        'router' => '/pwbill/index',
                    ],
                    [
                        'title' => '销售退货入库',
                        'router' => '/srbill/index',
                    ],
                    [
                        'title' => '其他出入库',
                        'router' => '/other-bill/index',
                    ],
                    [
                        'title' => '调拨出库',
                        'router' => '/transfer/index',
                    ],
                    [
                        'title' => '盘点',
                        'router' => '/icbill/index',
                    ],
                ],
            ],
            [
                'title' => '转运仓',
                'subs' => [
                    [
                        'title' => '转运件',
                        'router' => '/stocks/index',
                    ],
                    [
                        'title' => '货代转运件',
                        'router' => '/stocks/stock',
                    ],
                    [
                        'title' => '待出库列表',
                        'router' => '/stocks/outbound',
                    ],
                    [
                        'title' => '已出库列表',
                        'router' => '/stocks/outbound-list',
                    ],
                ],
            ],
        ],
    ],

    [
        'icon' => 'icon-information-outline',
        'index' => 'problems',
        'title' => '问题单',
        'subs' => [
            [
                'title' => '问题单管理',
                'subs' => [
                    [
                        'title' => '问题单列表',
                        'router' => '/problems/index',
                    ],

                ],
            ],
        ],
    ],

];
