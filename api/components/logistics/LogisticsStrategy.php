<?php

namespace api\components\logistics;

/**
 * 物流接口类 - 策略模式
 */
interface LogisticsStrategy
{
    public function push($data);
}
