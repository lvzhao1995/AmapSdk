<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\SimpleData;

class Segment extends SimpleData
{
    /**
     * @var TransitWalking
     */
    protected $walking;
    /**
     * @var TransitBus
     */
    protected $bus;
    /**
     * @var SubwayExit
     */
    protected $entrance;
    /**
     * @var SubwayExit
     */
    protected $exit;
    /**
     * @var Railway
     */
    protected $railway;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->walking = empty($data['walking']) ? null : new TransitWalking($data['walking']);
        $this->bus = empty($data['bus']) ? null : new TransitBus($data['bus']);
        $this->entrance = empty($data['entrance']) ? null : new SubwayExit($data['entrance']);
        $this->exit = empty($data['exit']) ? null : new SubwayExit($data['exit']);
        $this->railway = empty($data['railway']) ? null : new Railway($data['railway']);
    }

    /**
     * 此路段步行导航信息
     * @return TransitWalking
     */
    public function getWalking()
    {
        return $this->walking;
    }

    /**
     * 此路段公交导航信息
     * @return TransitBus
     */
    public function getBus()
    {
        return $this->bus;
    }

    /**
     * 地铁入口
     * @return SubwayExit
     */
    public function getEntrance()
    {
        return $this->entrance;
    }

    /**
     * 地铁出口
     * @return SubwayExit
     */
    public function getExit()
    {
        return $this->exit;
    }

    /**
     * 乘坐火车的信息
     * @return Railway
     */
    public function getRailway()
    {
        return $this->railway;
    }
}