<?php


namespace Amap\Result\Geo;


use Amap\Result\Base\SimpleData;

/**
 * Class ReGeoCode
 */
class ReGeoCode extends SimpleData
{
    /**
     * @var string
     */
    protected $formatted_address;
    /**
     * @var AddressComponent
     */
    protected $addressComponent;
    /**
     * @var Road[]
     */
    protected $roads;
    /**
     * @var Poi[]
     */
    protected $pois;
    /**
     * @var RoadInter[]
     */
    protected $roadinters;
    /**
     * @var Aoi[]
     */
    protected $aois;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->addressComponent = empty($data['addressComponent']) ? null : new AddressComponent($data['addressComponent']);
        if (!empty($data['roads'])) {
            $roads = [];
            foreach ($data['roads'] as $item) {
                empty($item) or $roads[] = new Road($item);
            }
            $this->roads = empty($roads) ? null : $roads;
        }
        if (!empty($data['roadinter'])) {
            $roadinters = [];
            foreach ($data['roadinter'] as $item) {
                empty($item) or $roadinters[] = new RoadInter($item);
            }
            $this->roadinters = empty($roadinters) ? null : $roadinters;
        }
        if (!empty($data['pois'])) {
            $pois = [];
            foreach ($data['pois'] as $item) {
                empty($item) or $pois[] = new Poi($item);
            }
            $this->pois = empty($pois) ? null : $pois;
        }
        if (!empty($data['aois'])) {
            $aois = [];
            foreach ($data['aois'] as $item) {
                empty($item) or $aois[] = new Poi($item);
            }
            $this->aois = empty($aois) ? null : $aois;
        }
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formatted_address;
    }

    /**
     * @return AddressComponent
     */
    public function getAddressComponent()
    {
        return $this->addressComponent;
    }

    /**
     * @return Road[]
     */
    public function getRoads()
    {
        return $this->roads;
    }

    /**
     * @return Poi[]
     */
    public function getPois()
    {
        return $this->pois;
    }

    /**
     * @return RoadInter[]
     */
    public function getRoadInters()
    {
        return $this->roadinters;
    }

    /**
     * @return Aoi[]
     */
    public function getAois()
    {
        return $this->aois;
    }
}