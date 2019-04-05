<?php


namespace Amap\Providers;


use Amap\Result\Base\Location;
use Amap\Result\Geo\GeoResult;
use Amap\Result\Geo\ReGeoResult;

class GeocodeProviders extends BaseProviders
{
    const GEO_URL = 'geocode/geo';
    const RE_GEO_URL = 'geocode/regeo';

    /**
     * 地理编码
     * @param string|array $address 结构化地址信息。如解析多个地址，用|分隔或传入数组，最多支持10个地址。
     * @param string $city 指定城市的中文（北京）、中文全拼（beijing）、citycode(010)、adcode(110000)，不支持县级市。当指定城市查询内容为空时，会进行全国范围内的地址转换检索。
     * @return GeoResult
     */
    public function geo($address, $city = null)
    {
        if (is_array($address)) {
            $address = implode('|', $address);
        }
        $params = [
            'key' => $this->config['key'],
            'address' => $address,
            'batch' => strpos($address, '|') !== false
        ];
        if (!is_null($city)) {
            $params['city'] = $city;
        }
        $params = $this->dealParams($params);
        if ($this->config['sign']) {
            $params['sig'] = $this->signature($params);
        }
        $result = $this->get(self::GEO_URL, $params);

        return new GeoResult($result);
    }

    /**
     * 逆地理编码
     * @param string $location 经纬度坐标,多个经纬度以"|"分隔或传入数组
     * @param int $radius 搜索半径，取值范围0~3000，单位：米
     * @param string $extensions 返回结果控制
     * @param null $poiType 返回附近poi类型，extensions为all是生效，多个坐标时不生效
     * @param null $roadLevel 道路等级
     * @param null $homeOrCorp 是否优化poi返回顺序
     * @return ReGeoResult
     */
    public function reGeo($location, $radius = 1000, $extensions = 'base', $poiType = null, $roadLevel = null, $homeOrCorp = null)
    {
        if (is_array($location)) {
            $location = implode('|', $location);
        }
        if ($location instanceof Location) {
            $location = $location->__toString();
        }
        $params = [
            'key' => $this->config['key'],
            'location' => $location,
            'radius' => $radius,
            'extensions' => $extensions,
            'batch' => strpos($location, '|') !== false
        ];
        if (!is_null($poiType)) {
            $params['poitype'] = $poiType;
        }
        if (!is_null($roadLevel)) {
            $params['roadlevel'] = $roadLevel;
        }
        if (!is_null($homeOrCorp)) {
            $params['homeorcorp'] = $homeOrCorp;
        }
        $params = $this->dealParams($params);
        if ($this->config['sign']) {
            $params['sig'] = $this->signature($params);
        }
        $result = $this->get(self::RE_GEO_URL, $params);

        return new ReGeoResult($result);
    }
}