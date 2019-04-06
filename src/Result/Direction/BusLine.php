<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

/**
 * 公交线路信息
 */
class BusLine extends SimpleData
{
    /**
     * @var BusStation
     */
    protected $departure_stop;
    /**
     * @var BusStation
     */
    protected $arrival_stop;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var number
     */
    protected $distance;
    /**
     * @var number
     */
    protected $duration;
    /**
     * @var string
     */
    protected $polyline;
    /**
     * @var string
     */
    protected $start_time;
    /**
     * @var string
     */
    protected $end_time;
    /**
     * @var number
     */
    protected $via_num;
    /**
     * @var BusStation[]
     */
    protected $via_stops;

    /**
     * @var Location[]
     */
    protected $polyline_array;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->departure_stop = empty($data['departure_stop']) ? null : new BusStation($data['departure_stop']);
        $this->arrival_stop = empty($data['arrival_stop']) ? null : new BusStation($data['arrival_stop']);
        if (!empty($data['polyline'])) {
            $polylines = explode(';', $data['polyline']);
            $this->polyline_array = [];
            foreach ($polylines as $item) {
                empty($item) or $this->polyline_array[] = new Location($item);
            }
        }
        if (!empty($data['via_stops'])) {
            $via_stops = [];
            foreach ($data['via_stops'] as $item) {
                empty($item) or $via_stops[] = new BusStation($item);
            }
            $this->via_stops = empty($via_stops) ? null : $via_stops;
        }
    }

    /**
     * 起乘站信息
     * @return BusStation
     */
    public function getDepartureStop()
    {
        return $this->departure_stop;
    }

    /**
     * 下车站
     * @return BusStation
     */
    public function getArrivalStop()
    {
        return $this->arrival_stop;
    }

    /**
     * 公交路线名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 公交线路id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 公交类型
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 公交行驶距离
     * @return number
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * 公交预计行驶时间
     * @return number
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * 此路段坐标集
     * @return string
     */
    public function getPolyline()
    {
        return $this->polyline;
    }

    /**
     * 首班车时间
     * @return string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * 末班车时间
     * @return string
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * 此段途经公交站数
     * @return BusStation[]
     */
    public function getViaStops()
    {
        return $this->via_stops;
    }

    /**
     * 此段途经公交站点列表
     * @return Location[]
     */
    public function getPolylineArray()
    {
        return $this->polyline_array;
    }
}