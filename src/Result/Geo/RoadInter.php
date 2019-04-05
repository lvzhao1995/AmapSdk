<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * Class RoadInter
 */
class RoadInter extends SimpleData
{
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
    /**
     * @var string
     */
    protected $first_id;
    /**
     * @var string
     */
    protected $first_name;
    /**
     * @var string
     */
    protected $second_id;
    /**
     * @var string
     */
    protected $second_name;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->location = empty($data['location']) ? null : new Location($data['location']);
    }

    /**
     * 交叉路口到请求坐标的距离
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * 第二条道路名称
     * @return string
     */
    public function getSecondName()
    {
        return $this->second_name;
    }

    /**
     * 第二条道路id
     * @return string
     */
    public function getSecondId()
    {
        return $this->second_id;
    }

    /**
     * 第一条道路名称
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * 输入点相对路口的方位
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * 路口经纬度
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * 第一条道路id
     * @return string
     */
    public function getFirstId()
    {
        return $this->first_id;
    }
}