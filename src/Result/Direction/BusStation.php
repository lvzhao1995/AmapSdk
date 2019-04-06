<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * 公交车站信息
 */
class BusStation extends SimpleData
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $id;
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
     * 车站名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 车站id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 车站坐标
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}