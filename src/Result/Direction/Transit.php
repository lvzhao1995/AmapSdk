<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\SimpleData;

class Transit extends SimpleData
{
    /**
     * @var number
     */
    protected $cost;
    /**
     * @var number
     */
    protected $duration;
    /**
     * @var number
     */
    protected $nightflag;
    /**
     * @var number
     */
    protected $working_distance;
    /**
     * @var Segment[]
     */
    protected $segments;

    public function __construct($data)
    {
        parent::__construct($data);
        if (!empty($data['segments'])) {
            $segments = [];
            foreach ($data['segments'] as $item) {
                empty($item) or $segments[] = new Segment($item);
            }
            $this->segments = empty($segments) ? null : $segments;
        }
    }

    /**
     * 此换乘方案价格
     * @return number
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * 此换乘方案预期时间
     * @return number
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * 是否是夜班车
     * @return number
     */
    public function getNightFlag()
    {
        return $this->nightflag;
    }

    /**
     * 此方案总步行距离
     * @return number
     */
    public function getWorkingDistance()
    {
        return $this->working_distance;
    }

    /**
     * 换乘路段列表
     * @return Segment[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

}