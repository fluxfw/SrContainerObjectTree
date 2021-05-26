<?php

namespace srag\DIC\SrContainerObjectTree\DIC;

use ILIAS\DI\Container;
use srag\DIC\SrContainerObjectTree\Database\DatabaseDetector;
use srag\DIC\SrContainerObjectTree\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\SrContainerObjectTree\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
