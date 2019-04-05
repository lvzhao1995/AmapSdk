<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\SimpleData;

/**
 * Class Neighborhood 社区信息
 */
class Neighborhood extends SimpleData
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $type;

    /**
     * 获取社区名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 获取POI类型，多个类型以英文分号分割
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 获取POI类型数组
     * @return array
     */
    public function getTypeArray()
    {
        return explode(';', $this->type);
    }

}