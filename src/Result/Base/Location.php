<?php


namespace Amap\Result\Base;

/**
 * 坐标点辅助类
 * @package Amap\Result\Base
 */
class Location
{
    protected $longitude;
    protected $latitude;

    public function __construct($longitude, $latitude = '')
    {
        if (strpos($longitude, ',') !== false) {
            list($longitude, $latitude) = explode(',', $longitude);
        }
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    public function __toString()
    {
        return $this->longitude . ',' . $this->latitude;
    }
}