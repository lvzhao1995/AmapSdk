<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\BaseResult;

class TransitResult extends BaseResult
{
    /**
     * @var number
     */
    protected $count;
    /**
     * @var TransitRoute
     */
    protected $route;

    public function __construct($result)
    {
        parent::__construct($result);
        if (!empty($result['route'])) {
            $this->route = new TransitRoute($result['route']);
        }
    }

    /**
     * 公交换乘方案数目
     * @return number
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * 公交换乘信息列表
     * @return TransitRoute
     */
    public function getRoute()
    {
        return $this->route;
    }
}