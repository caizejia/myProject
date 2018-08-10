<?php

// RESTful API 映射控制器方法
return [
    // 配置允许vue预检请求 OPTIONS
    'OPTIONS /<path:.+>' => '/oms/base/options',

    // action测试代码
    'GET /test' => '/oms/default/test',

    // 后台用户
    'POST /v1/tokens' => '/oms/user/token',

    // 产品分类
    'GET /v1/categories' => '/oms/category/index',
    'POST /v1/categories' => '/oms/category/create',
    'PUT /v1/categories/<id:\d+>' => '/oms/category/update',
    'DELETE /v1/categories/<id:\d+>' => '/oms/category/delete',
    'GET /v1/category/initdatas' => '/oms/category/init-data',

    // 域名
    'GET /v1/websites' => '/oms/ad-website/index',
    'POST /v1/websites' => '/oms/ad-website/create',
    'PUT /v1/websites/<id:\d+>' => '/oms/ad-website/update',
    'DELETE /v1/websites/<id:\d+>' => '/oms/ad-website/delete',

    // 产品供应商
    'GET /v1/suppliers' => '/oms/supplier/index',
    'POST /v1/suppliers' => '/oms/supplier/create',
    'PUT /v1/suppliers/<id:\d+>' => '/oms/supplier/update',
    'DELETE /v1/suppliers/<id:\d+>' => '/oms/supplier/delete',

    // 产品品牌
    'GET /v1/brands' => '/oms/brand/index',
    'POST /v1/brands' => '/oms/brand/create',
    'PUT /v1/brands/<id:\d+>' => '/oms/brand/update',
    'DELETE /v1/brands/<id:\d+>' => '/oms/brand/delete',

    // 评论管理
    'GET /v1/reviews' => '/oms/reviews/index',
    'POST /v1/reviews' => '/oms/reviews/create',
    'PUT /v1/reviews/<id:\d+>' => '/oms/reviews/update',
    'DELETE /v1/reviews/<id:\d+>' => '/oms/reviews/delete',

    // 产品中心
    'GET /v1/centers' => '/oms/product-center/index',
    'POST /v1/centers' => '/oms/product-center/create',
    'PUT /v1/centers/<id:\d+>' => '/oms/product-center/update',
    'POST /v1/repeats' => '/oms/product-center/update-repeat',
    'GET /v1/images' => '/oms/product-center/que-ren',

    // 翻译任务
    'GET /v1/translations' => '/oms/translation/index',
    'GET /v1/translations/<id:\d+>' => '/oms/translation/detail',
    'POST /v1/translations' => '/oms/translation/create',
    'POST /v1/translations/fix-users' => '/oms/translation/fix-user',
    'POST /v1/translations/ajax-status' => '/oms/translation/ajax-status',
    'GET /v1/translations/user-name' => '/oms/translation/user-name',
    'PUT /v1/translations/<id:\d+>' => '/oms/translation/update',
    'DELETE /v1/translations/<id:\d+>' => '/oms/translation/delete',

    // 产品列表
    'GET /v1/products' => '/oms/product/index',
    'GET /v1/products/<id:\d+>' => '/oms/product/detail',
    'POST /v1/products' => '/oms/product/create',
    'PUT /v1/products/<id:\d+>' => '/oms/product/update',
    'DELETE /v1/products/<id:\d+>' => '/oms/product/delete',
    'GET /v1/product/initdatas' => '/oms/product/init-data',

    // 产品发布列表
    'GET /v1/product/releases' => '/oms/product-release/index',
    'GET /v1/product/releases/<id:\d+>' => '/oms/product-release/detail',
    'POST /v1/product/releases' => '/oms/product-release/create',
    'PUT /v1/product/releases/<id:\d+>' => '/oms/product-release/update',
    'DELETE /v1/product/releases/<id:\d+>' => '/oms/product-release/delete',
    'POST /v1/product/disable' => '/oms/product-release/ajax-disable',
    'GET /v1/release/initdatas' => '/oms/product-release/init-data',

    // 客户中心列表
    'GET /v1/customer' => '/oms/customer/index',
    'POST /v1/customer' => '/oms/customer/create',
    'PUT /v1/customer/<id:\d+>' => '/oms/customer/update',
    'DELETE /v1/customer/<id:\d+>' => '/oms/customer/delete',

    // 销售城市
    'GET /v1/salescitys' => '/oms/sales-city/index',
    'POST /v1/salescitys' => '/oms/sales-city/create',
    'PUT /v1/salescitys/<id:\d+>' => '/oms/sales-city/update',
    'DELETE /v1/salescitys/<id:\d+>' => '/oms/sales-city/delete',

    // 产品评论
    'GET /v1/comments' => '/oms/comments/index',
    'POST /v1/comments' => '/oms/comments/create',
    'PUT /v1/comments/<id:\d+>' => '/oms/comments/update',
    'DELETE /v1/comments/<id:\d+>' => '/oms/comments/delete',
    'GET /v1/comments/<id:\d+>' => '/oms/comments/detail',

    // 订单
    'GET /v1/orders' => '/oms/order/index',
    'POST /v1/orders' => '/oms/order/create',
    'PUT /v1/orders/<id:\d+>' => '/oms/order/update',
    'DELETE /v1/orders/<id:\d+>' => '/oms/order/delete',
    'GET /v1/order/searches' => '/oms/order/search',
    'GET /v1/order/details' => '/oms/order/detail',
    'POST /v1/order/update-status' => '/oms/order/update-status',
    'POST /v1/order/new-order' => '/oms/order/new-order',
    'GET /v1/order/audit-order' => '/oms/order/audit-order',
    'POST /v1/order/update-order' => '/oms/order/update-order',
    'POST /v1/order/order-attr' => '/oms/order/order-attr',
    'POST /v1/order/update-product-orders' => '/oms/order/update-product-orders',

    // 产品排重检查
    'GET /v1/product/checks' => '/oms/product-check/index',
    'POST /v1/product/checks' => '/oms/product-check/create',
    'DELETE /v1/product/checks/<id:\d+>' => '/oms/product-check/delete',
    'PUT /v1/product/checks/<id:\d+>' => '/oms/product-check/update',

    // 问题件
    'GET /v1/problems' => '/oms/problems/index',
    'POST /v1/problems' => '/oms/problems/create',
    'PUT /v1/problems/<id:\d+>' => '/oms/problems/update',
    'DELETE /v1/problems/<id:\d+>' => '/oms/problems/delete',

    // 问题件日志
    'GET /v1/problems-log' => '/oms/problemsLog/index',
    'POST /v1/problems-log' => '/oms/problemsLog/create',
    'PUT /v1/problems-log/<id:\d+>' => '/oms/problemsLog/update',
    'DELETE /v1/problems-log/<id:\d+>' => '/oms/problemsLog/delete',

    // 属性
    'GET /v1/attribute-key' => '/oms/attribute-key/index',
    'POST /v1/attribute-key' => '/oms/attribute-key/create',
    'PUT /v1/attribute-key/<id:\d+>' => '/oms/attribute-key/update',
    'DELETE /v1/attribute-key/<id:\d+>' => '/oms/attribute-key/delete',

    // ueditor编辑器
    'GET /v1/ueditors' => '/oms/ueditor/ueditor',
    'POST /v1/ueditors' => '/oms/ueditor/ueditor',

    // 问题件
    'GET /v2/purs' => '/v2/purchases/index',

    // 语言切换 
    'GET /language/<lang:.+>' => '/site/language',

];
