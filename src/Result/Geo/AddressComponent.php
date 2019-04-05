<?php

namespace Amap\Result\Geo;

use Amap\Result\Base\SimpleData;

/**
 * Class AddressComponent
 */
class AddressComponent extends SimpleData
{
    /**
     * @var string
     */
    protected $country;
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
    protected $adcode;
    /**
     * @var string
     */
    protected $township;
    /**
     * @var string
     */
    protected $towncode;
    /**
     * @var string
     */
    protected $seaArea;
    /**
     * @var Neighborhood
     */
    protected $neighborhood;
    /**
     * @var Building
     */
    protected $building;
    /**
     * @var StreetNumber
     */
    protected $streetNumber;
    /**
     * @var BusinessArea[]
     */
    protected $businessAreas;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->neighborhood = empty($data['neighborhood']) ? null : new Neighborhood($data['neighborhood']);
        $this->building = empty($data['building']) ? null : new Building($data['building']);
        $this->streetNumber = empty($data['streetNumber']) ? null : new StreetNumber($data['streetNumber']);
        if (!empty($data['businessAreas'])) {
            $businessAreas = [];
            foreach ($data['businessAreas'] as $item) {
                empty($item) or $businessAreas[] = new BusinessArea($item);
            }
            $this->businessAreas = empty($businessAreas) ? null : $businessAreas;
        }
    }

    /**
     * 国家
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
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
     * 行政区编码
     * @return string
     */
    public function getAdcode()
    {
        return $this->adcode;
    }

    /**
     * 乡镇/街道
     * @return string
     */
    public function getTownship()
    {
        return $this->township;
    }

    /**
     * 乡镇街道编码
     * @return string
     */
    public function getTownCode()
    {
        return $this->towncode;
    }

    /**
     * 海域
     * @return string
     */
    public function getSeaArea()
    {
        return $this->seaArea;
    }

    /**
     * 社区信息
     * @return Neighborhood
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * 楼信息
     * @return Building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * 门牌信息
     * @return StreetNumber
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * 所属商圈列表
     * @return BusinessArea[]
     */
    public function getBusinessAreas()
    {
        return $this->businessAreas;
    }
}