<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\BaseResult;

class WalkingResult extends BaseResult
{
    /**
     * @var number
     */
    protected $count;
    /**
     * @var WalkRoute
     */
    protected $route;

    public function __construct($result)
    {
        parent::__construct($result);
        if (!empty($result['route'])) {
            $this->route = new WalkRoute($result['route']);
        }

    }

    /**
     * 返回结果总数目
     * @return number
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * 路线信息
     * @return WalkRoute
     */
    public function getRoute()
    {
        return $this->route;
    }

}