<?php


namespace Amap\Result\Base;

use Amap\Amap;
use Amap\Result\Direction\WalkingResult;
use Amap\Result\Geo\ReGeoResult;

/**
 * 坐标点辅助类
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

    /**
     * 快捷调用逆地理编码
     * @param Amap $app
     * @return ReGeoResult
     */
    public function reGeo(Amap $app)
    {
        return $app->geocode()->reGeo($this);
    }

    /**
     * 步行导航
     * @param Amap $app
     * @param string|Location $destination 目的地
     * @return WalkingResult
     */
    public function walkTo(Amap $app, $destination)
    {
        return $app->direction()->walking($this, $destination);
    }

    public function transitTo(Amap $app, $destination, $city)
    {
        return $app->direction()->transit($this, $destination, $city);
    }
}