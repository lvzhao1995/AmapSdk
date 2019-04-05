<?php

namespace Amap;

use Amap\Kernel\Config;
use Amap\Providers\DirectionProviders;
use Amap\Providers\GeocodeProviders;

/**
 * Class Application
 * @package Amap
 * @method GeocodeProviders geocode()
 * @method DirectionProviders direction()
 */
class Amap
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    public function __call($name, $arguments)
    {
        $classname = '\\Amap\\Providers\\' . ucfirst($name) . 'Providers';
        return new $classname($this->config, ...$arguments);
    }
}