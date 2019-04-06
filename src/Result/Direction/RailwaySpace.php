<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\SimpleData;

class RailwaySpace extends SimpleData
{
    /**
     * @var string
     */
    protected $code;
    /**
     * @var number
     */
    protected $cost;

    /**
     * 仓位编码
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 仓位费用
     * @return number
     */
    public function getCost()
    {
        return $this->cost;
    }

}