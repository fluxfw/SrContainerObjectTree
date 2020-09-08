<?php

namespace srag\Plugins\SrCurriculum\Utils;

use srag\Plugins\SrCurriculum\Repository;

/**
 * Trait SrCurriculumTrait
 *
 * @package srag\Plugins\SrCurriculum\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait SrCurriculumTrait
{

    /**
     * @return Repository
     */
    protected static function srCurriculum() : Repository
    {
        return Repository::getInstance();
    }
}
