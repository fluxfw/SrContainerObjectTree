<?php

namespace srag\ActiveRecordConfig\SrContainerObjectTree\Config;

use srag\DIC\SrContainerObjectTree\DICTrait;

/**
 * Class AbstractFactory
 *
 * @package srag\ActiveRecordConfig\SrContainerObjectTree\Config
 */
abstract class AbstractFactory
{

    use DICTrait;

    /**
     * AbstractFactory constructor
     */
    protected function __construct()
    {

    }


    /**
     * @return Config
     */
    public function newInstance() : Config
    {
        $config = new Config();

        return $config;
    }
}
