<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * Class Poi
 */
class Poi extends SimpleData
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
    protected $type;
    /**
     * @var string
     */
    protected $tel;
    /**
     * @var string
     */
    protected $distance;
    /**
     * @var string
     */
    protected $direction;
    /**
     * @var string
     */
    protected $address;
    /**
     * @var Location
     */
    protected $location;
    /**
     * @var string
     */
    protected $businessarea;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->location = empty($data['location']) ? null : new Location($data['location']);
    }

    /**
     * poi的id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * poi点名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * poi类型
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 电话
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * 该POI到请求坐标的距离
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * 方向
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * poi地址信息
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
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
     * poi所在商圈名称
     * @return string
     */
    public function getBusinessArea()
    {
        return $this->businessarea;
    }
}