<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * Class StreetNumber 门牌信息
 */
class StreetNumber extends SimpleData
{
    /**
     * @var string
     */
    protected $street;
    /**
     * @var string
     */
    protected $number;
    /**
     * @var Location
     */
    protected $location;
    /**
     * @var string
     */
    protected $direction;
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
     * 街道名称
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * 门牌号
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * 坐标点
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * 坐标点所处街道方位
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * 门牌地址到请求坐标的距离
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }
}