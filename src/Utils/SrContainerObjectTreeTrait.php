<?php

namespace srag\Plugins\SrContainerObjectTree\Utils;

use srag\Plugins\SrContainerObjectTree\Repository;

/**
 * Trait SrContainerObjectTreeTrait
 *
 * @package srag\Plugins\SrContainerObjectTree\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
