<?php


namespace Amap\Providers;


use Amap\Result\Base\Location;
use Amap\Result\Direction\TransitResult;
use Amap\Result\Direction\WalkingResult;

class DirectionProviders extends BaseProviders
{
    const WALK_URL = 'direction/walking';
    const BUS_URL = 'direction/transit/integrated';

    /**
     * 步行导航
     * @param Location|string $origin 出发地
     * @param Location|string $destination 目的地
     * @return WalkingResult
     */
    public function walking($origin, $destination)
    {
        if ($origin instanceof Location) {
            $origin = $origin->__toString();
        }
        if ($destination instanceof Location) {
            $destination = $destination->__toString();
        }
        $params = [
            'key' => $this->config['key'],
            'origin' => $origin,
            'destination' => $destination
        ];
        if ($this->config['sign']) {
            $params['sig'] = $this->signature($params);
        }

        $result = $this->get(self::WALK_URL, $params);
        return new WalkingResult($result);
    }

    /**
     * @param Location|string $origin 出发点
     * @param Location|string $destination 目的地
     * @param string $city 起点城市
     * @param string $cityd 终点城市，跨城公交必填
     * @param int $strategy 公交换乘策略
     * @param string $extensions 返回结果详略
     * @param int $nightflag 是否计算夜班车
     * @param string|int $date 出发日期,传入数字时作为时间戳同时填充$date和$time
     * @param string $time 出发时间
     * @return TransitResult
     */
    public function transit($origin, $destination, $city, $cityd = null, $strategy = 0, $extensions = 'base', $nightflag = 0, $date = null, $time = null)
    {
        if ($origin instanceof Location) {
            $origin = $origin->__toString();
        }
        if ($destination instanceof Location) {
            $destination = $origin->__toString();
        }
        $params = [
            'key' => $this->config['key'],
            'origin' => $origin,
            'destination' => $destination,
            'city' => $city,
            'extensions' => $extensions,
            'strategy' => $strategy,
            'nightflag' => $nightflag
        ];
        if (!is_null($cityd)) {
            $params['cityd'] = $cityd;
        }
        if (!is_null($date)) {
            if (is_numeric($date)) {
                $params['date'] = date('Y-m-d', $date);
                $params['time'] = date('H:i', $date);
            } else {
                $params['date'] = $date;
            }
        }
        if (!is_null($time)) {
            $params['time'] = $time;
        }
        if ($this->config['sign']) {
            $params['sig'] = $this->signature($params);
        }
        $result = $this->get(self::BUS_URL, $params);

        return new TransitResult($result);
    }
}