<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * 火车站信息
 */
class RailwayStation extends SimpleData
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
     * @var Location
     */
    protected $location;
    /**
     * @var string
     */
    protected $adcode;
    /**
     * @var string
     */
    protected $time;
    /**
     * @var number
     */
    protected $start;
    /**
     * @var number
     */
    protected $end;
    /**
     * @var number
     */
    protected $wait;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->location = empty($data['location']) ? null : new Location($data['location']);
    }

    /**
     * 站点id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 站点名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 站点坐标
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * 站点所在城市adcode，途径站无此信息
     * @return string
     */
    public function getAdcode()
    {
        return $this->adcode;
    }

    /**
     * 上车站发车时间/到站时间
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * 是否始发站，1表示为始发站，0表示非始发站
     * @return number
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * 是否为终点站，1表示为终点站，0表示非终点站
     * @return number
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * 途径站点的停靠时间
     * @return number
     */
    public function getWait()
    {
        return $this->wait;
    }

}