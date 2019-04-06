<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;

class TransitWalking extends Path
{
    /**
     * @var Location
     */
    protected $destination;
    /**
     * @var Location
     */
    protected $origin;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->origin = empty($data['origin']) ? null : new Location($data['origin']);
        $this->destination = empty($data['destination']) ? null : new Location($data['destination']);
    }

    /**
     * 起点坐标
     * @return Location
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * 终点坐标
     * @return Location
     */
    public function getOrigin()
    {
        return $this->origin;
    }
}