<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * 地铁出入口信息
 */
class SubwayExit extends SimpleData
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var Location
     */
    protected $location;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->location = empty($data['location']) ? null : new Location($data['location']);
    }

    /**
     * 出入口名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 出入口坐标
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

}