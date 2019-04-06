<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\SimpleData;

class TransitBus extends SimpleData
{
    /**
     * @var BusLine[]
     */
    protected $buslines;

    public function __construct($data)
    {
        parent::__construct($data);
        if (!empty($data['buslines'])) {
            $busLines = [];
            foreach ($data['buslines'] as $item) {
                empty($item) or $busLines[] = new BusLine($item);
            }
            $this->buslines = empty($busLines) ? null : $busLines;
        }
    }

    /**
     * 公交路段列表
     * @return BusLine[]
     */
    public function getBusLines()
    {
        return $this->buslines;
    }

}