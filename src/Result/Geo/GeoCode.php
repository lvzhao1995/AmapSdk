<?php

namespace Amap\Result\Geo;

use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**+
 * Class GeoCode
 */
class GeoCode extends SimpleData
{

    /**
     * @var string
     */
    protected $formatted_address;
    /**
     * @var string
     */
    protected $province;
    /**
     * @var string
     */
    protected $city;
    /**
     * @var string
     */
    protected $citycode;
    /**
     * @var string
     */
    protected $district;
    /**
     * @var string
     */
    protected $township;
    /**
     * @var string
     */
    protected $street;
    /**
     * @var string
     */
    protected $number;
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
    protected $level;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->location = empty($data['location']) ? null : new Location($data['location']);
    }

    /**
     * 结构化地址信息
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formatted_address;
    }

    /**
     * 省份
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * 城市
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * 城市编码
     * @return string
     */
    public function getCityCode()
    {
        return $this->citycode;
    }

    /**
     * 区
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * 乡镇
     * @return string
     */
    public function getTownship()
    {
        return $this->township;
    }

    /**
     * 街道
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * 门牌
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * 区域编码
     * @return string
     */
    public function getAdcode()
    {
        return $this->adcode;
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
     * 匹配级别
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

}