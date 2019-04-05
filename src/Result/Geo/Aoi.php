<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

class Aoi extends SimpleData
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
    protected $adcode;
    /**
     * @var Location
     */
    protected $location;
    /**
     * @var string
     */
    protected $area;
    /**
     * @var string
     */
    protected $distance;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->location = empty($data['location']) ? null : new Location($data['location']);
    }

    /**
     * aoi的id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * aoi的名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * aoi所在区域编码
     * @return string
     */
    public function getAdcode()
    {
        return $this->adcode;
    }

    /**
     * aoi中心点坐标
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * aoi点面积
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * 坐标点距离aoi距离，0代表在aoi内
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }
}