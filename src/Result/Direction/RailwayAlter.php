<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\SimpleData;

class RailwayAlter extends SimpleData
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;

    /**
     * 备选方案ID
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 备选线路名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}