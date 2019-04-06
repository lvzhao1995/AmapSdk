<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\SimpleData;

class Path extends SimpleData
{
    /**
     * @var number
     */
    protected $distance;
    /**
     * @var number
     */
    protected $duration;
    /**
     * @var Step[]
     */
    protected $steps;

    public function __construct($data)
    {
        parent::__construct($data);
        if (!empty($data['steps'])) {
            $steps = [];
            foreach ($data['steps'] as $item) {
                empty($item) or $steps[] = new Step($item);
            }
            $this->steps = $steps;
        }
    }

    /**
     * 起点和终点的步行距离
     * @return number
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * 步行时间预计
     * @return number
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * 步行结果列表
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

}