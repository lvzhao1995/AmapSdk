<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

class TransitRoute extends SimpleData
{
    /**
     * @var Location
     */
    protected $origin;
    /**
     * @var Location
     */
    protected $destination;
    /**
     * @var number
     */
    protected $taxi_cost;
    /**
     * @var Transit[]
     */
    protected $transits;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->origin = empty($data['origin']) ? null : new Location($data['origin']);
        $this->destination = empty($data['destination']) ? null : new Location($data['destination']);
        if (!empty($data['transits'])) {
            $transits = [];
            foreach ($data['transits'] as $item) {
                empty($item) or $transits[] = new Transit($item);
            }
            $this->transits = empty($transits) ? null : $transits;
        }
    }

    /**
     * 起点坐标
     * @return Location
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * 终点坐标
     * @return Location
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * 出租车费用
     * @return number
     */
    public function getTaxiCost()
    {
        return $this->taxi_cost;
    }

    /**
     * 公交换乘方案列表
     * @return Transit[]
     */
    public function getTransits()
    {
        return $this->transits;
    }
}