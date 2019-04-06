<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

class WalkRoute extends SimpleData
{
    /**
     * @var Location
     */
    protected $origin;
    /**
     * @var Location
     */
    protected $destination;
    /**
     * @var Path[]
     */
    protected $paths;

    public function __construct($data)
    {
        $this->origin = empty($data['origin']) ? null : new Location($data['origin']);
        $this->destination = empty($data['destination']) ? null : new Location($data['destination']);
        if (!empty($data['paths'])) {
            $paths = [];
            foreach ($data['paths'] as $item) {
                empty($item) or $paths[] = new Path($item);
            }
            $this->paths = empty($paths) ? null : $paths;
        }
    }

    /**
     * 起点坐标
     * @return Location
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * 终点坐标
     * @return Location
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * 步行方案
     * @return Path[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

}