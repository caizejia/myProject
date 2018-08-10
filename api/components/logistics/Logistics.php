<?php

namespace api\components\logistics;

/**
 * 环境角色类
 * @author YXH 
 * @date 2018/07/13
 */
class Logistics
{
    private $logisticsStrategy;

    public function __construct(LogisticsStrategy $logisticsStrategy)
    {
        $this->logisticsStrategy = $logisticsStrategy;
    }

    public function pushOrder($data)
    {
        return $this->logisticsStrategy->push($data);
    }
}
