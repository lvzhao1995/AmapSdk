<?php


namespace Amap\Result\Direction;


use Amap\Result\Base\Location;
use Amap\Result\Base\SimpleData;

class Step extends SimpleData
{
    protected $walk_type_list = [
        '0' => '普通道路',
        '1' => '人行横道',
        '3' => '地下通道',
        '4' => '过街天桥',
        '5' => '地铁通道',
        '6' => '公园',
        '7' => '广场',
        '8' => '扶梯',
        '9' => '直梯',
        '10' => '索道',
        '11' => '空中通道',
        '12' => '建筑物穿越通道',
        '13' => '行人通道',
        '14' => '游船路线',
        '15' => '观光车路线',
        '16' => '滑道',
        '18' => '扩路',
        '19' => '道路附属连接线',
        '20' => '阶梯',
        '21' => '斜坡',
        '22' => '桥',
        '23' => '隧道',
        '30' => '轮渡',
    ];

    /**
     * @var string
     */
    protected $instruction;
    /**
     * @var string
     */
    protected $road;
    /**
     * @var number
     */
    protected $distance;
    /**
     * @var string
     */
    protected $orientation;
    /**
     * @var number
     */
    protected $duration;
    /**
     * @var string
     */
    protected $polyline;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var string
     */
    protected $assistant_action;
    /**
     * @var number
     */
    protected $walk_type;

    /**
     * @var Location[]
     */
    protected $polyline_array;
    /**
     * @var string
     */
    protected $walk_type_value;

    public function __construct($data)
    {
        parent::__construct($data);
        if (!empty($data['polyline'])) {
            $polylines = explode(';', $data['polyline']);
            $this->polyline_array = [];
            foreach ($polylines as $item) {
                empty($item) or $this->polyline_array[] = new Location($item);
            }
        }
        $this->walk_type_value = isset($this->walk_type_list[$this->walk_type]) ? $this->walk_type_list[$this->walk_type] : null;
    }

    /**
     * 路段步行指示
     * @return string
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * 道路名称
     * @return string
     */
    public function getRoad()
    {
        return $this->road;
    }

    /**
     * 此路段距离
     * @return number
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * 方向
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * 此路段坐标点
     * @return string
     */
    public function getPolyline()
    {
        return $this->polyline;
    }

    /**
     * 步行主要动作
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * 步行辅助动作
     * @return string
     */
    public function getAssistantAction()
    {
        return $this->assistant_action;
    }

    /**
     * 这段路是否存在特殊的方式
     * @return number
     */
    public function getWalkType()
    {
        return $this->walk_type;
    }

    /**
     * 此路段坐标点数组
     * @return Location[]
     */
    public function getPolylineArray()
    {
        return $this->polyline_array;
    }

    /**
     * 此路段特殊方式描述
     * @return string
     */
    public function getWalkTypeValue()
    {
        return $this->walk_type_value;
    }

    /**
     * 此路段预计步行时间
     * @return number
     */
    public function getDuration()
    {
        return $this->duration;
    }

}