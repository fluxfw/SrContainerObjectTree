<?php

namespace srag\Plugins\SrContainerObjectTree\Utils;

use srag\Plugins\SrContainerObjectTree\Repository;

/**
 * Trait SrContainerObjectTreeTrait
 *
 * @package srag\Plugins\SrContainerObjectTree\Utils
 */
trait SrContainerObjectTreeTrait
{

    /**
     * @return Repository
     */
    protected static function srContainerObjectTree() : Repository
    {
        return Repository::getInstance();
    }
}
