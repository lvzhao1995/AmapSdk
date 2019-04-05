<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * Class BusinessArea
 */
class BusinessArea extends SimpleData
{
    /**
     * @var Location
     */
    protected $location;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $id;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->location = empty($data['location']) ? null : new Location($data['location']);
    }

    /**
     * 商圈中心点经纬度
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * 商圈名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 商圈所在区域的adcode
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}