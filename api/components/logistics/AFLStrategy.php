<?php

namespace api\components\logistics;

use api\components\logistics\LogisticsStrategy;

/**
 * AFL 物流
 */
class AFLStrategy implements LogisticsStrategy
{
    // 请求地址
    private $url;

    // key
    private $key;

    // merchantid
    private $merchantId;

    public function push($data)
    {
        return 2;
    }
}
