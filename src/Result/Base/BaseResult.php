<?php


namespace Amap\Result\Base;


class BaseResult extends SimpleData
{
    /**
     * @var int
     */
    protected $status;
    /**
     * @var string
     */
    protected $info;
    /**
     * @var number
     */
    protected $infocode;
    /**
     * @var array
     */
    protected $result;

    public function __construct($result)
    {
        $this->status = empty($result['status']) ? null : $result['status'];
        $this->info = empty($result['info']) ? null : $result['info'];
        $this->result = $result;
    }

    /**
     * 返回状态说明
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * 获取状态
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 获取原始返回数据
     * @return array
     */
    public function getOrigin()
    {
        return $this->result;
    }

    /**
     * @return number
     */
    public function getInfoCode()
    {
        return $this->infocode;
    }
}