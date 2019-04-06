<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\SimpleData;

class Railway extends SimpleData
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var number
     */
    protected $time;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $trip;
    /**
     * @var number
     */
    protected $distance;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var RailwayStation
     */
    protected $departure_stop;
    /**
     * @var RailwayStation
     */
    protected $arrival_stop;
    /**
     * @var RailwayStation[]
     */
    protected $via_stops;
    /**
     * @var RailwayAlter[]
     */
    protected $alters;
    /**
     * @var RailwaySpace[]
     */
    protected $spaces;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->departure_stop = empty($data['departure_stop']) ? null : new RailwayStation($data['departure_stop']);
        $this->arrival_stop = empty($data['arrival_stop']) ? null : new RailwayStation($data['arrival_stop']);
        if (!empty($data['via_stops'])) {
            $viaStops = [];
            foreach ($data['via_stops'] as $item) {
                empty($item) or $viaStops[] = new RailwayStation($item);
            }
            $this->via_stops = $viaStops;
        }
        if (!empty($data['alters'])) {
            $alters = [];
            foreach ($data['alters'] as $item) {
                empty($item) or $alters[] = new RailwayAlter($item);
            }
            $this->alters = $alters;
        }
        if (!empty($data['spaces'])) {
            $spaces = [];
            foreach ($data['spaces'] as $item) {
                empty($item) or $spaces[] = new RailwaySpace($item);
            }
            $this->spaces = $spaces;
        }
    }

    /**
     * 线路id编号
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 该线路车段耗时
     * @return number
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * 线路名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 线路车次号
     * @return string
     */
    public function getTrip()
    {
        return $this->trip;
    }

    /**
     * 该item换乘段的行车总距离
     * @return number
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * 线路车次类型
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 火车始发站信息
     * @return RailwayStation
     */
    public function getDepartureStop()
    {
        return $this->departure_stop;
    }

    /**
     * 火车到站信息
     * @return RailwayStation
     */
    public function getArrivalStop()
    {
        return $this->arrival_stop;
    }

    /**
     * 途径站点信息，extensions=all时返回
     * @return RailwayStation[]
     */
    public function getViaStops()
    {
        return $this->via_stops;
    }

    /**
     * 聚合的备选方案，extensions=all时返回
     * @return RailwayAlter[]
     */
    public function getAlters()
    {
        return $this->alters;
    }

    /**
     * 仓位及价格信息
     * @return RailwaySpace[]
     */
    public function getSpaces()
    {
        return $this->spaces;
    }

}