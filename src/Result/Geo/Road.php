<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * Class Road
 */
class Road extends SimpleData
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $distance;
    /**
     * @var string
     */
    protected $direction;
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
     * 道路id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 道路名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 道路到请求坐标的距离
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * 方位
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * 坐标点
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

}