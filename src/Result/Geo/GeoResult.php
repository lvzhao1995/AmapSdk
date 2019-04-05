<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\BaseResult;

/**
 * Class GeoResult
 */
class GeoResult extends BaseResult
{
    /**
     * @var GeoCode[]
     */
    protected $geocodes;
    /**
     * @var int
     */
    protected $count;

    public function __construct($result)
    {
        parent::__construct($result);
        if (array_key_exists('geocodes', $result)) {
            $geoCodes = [];
            foreach ($result['geocodes'] as $item) {
                empty($item) or $geoCodes[] = new GeoCode($item);
            }
            $this->geocodes = empty($geoCodes) ? null : $geoCodes;
        }
    }

    /**
     * 获取地理编码信息列表
     * @return GeoCode[]
     */
    public function getGeoCodes()
    {
        return $this->geocodes;
    }

    /**
     * 获取返回结果数目
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}