<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\SimpleData;

/**
 * Class Building 楼信息
 */
class Building extends SimpleData
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
     * 获取建筑名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 获取类型
     * @return string 以分号分隔的多个类型
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 获取类型
     * @return array 类型数组
     */
    public function getTypeArray()
    {
        return explode(';', $this->type);
    }

}