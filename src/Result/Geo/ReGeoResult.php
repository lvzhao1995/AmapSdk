<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\BaseResult;

/**
 * Class ReGeoResult
 */
class ReGeoResult extends BaseResult
{
    /**
     * @var ReGeoCode[]
     */
    protected $regeocodes;

    public function __construct($result)
    {
        parent::__construct($result);
        if (array_key_exists('regeocodes', $result)) {
            $reGeoCodes = [];
            foreach ($result['regeocodes'] as $item) {
                $reGeoCodes[] = new ReGeoCode($item);
            }
            $this->regeocodes = $reGeoCodes;
        } elseif (array_key_exists('regeocode', $result)) {
            $this->regeocodes = [new ReGeoCode($result['regeocode'])];
        }
    }

    /**
     * @return ReGeoCode[]
     */
    public function getReGeoCodes()
    {
        return $this->regeocodes;
    }
}