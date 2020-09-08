<?php

namespace srag\Plugins\SrCurriculum;

use ilSrCurriculumPlugin;
use srag\DIC\SrCurriculum\DICTrait;
use srag\Plugins\SrCurriculum\ObjectSettings\Repository as ObjectSettingsRepository;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrCurriculum
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrCurriculumTrait;

    const PLUGIN_CLASS_NAME = ilSrCurriculumPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     *
     */
    public function dropTables()/* : void*/
    {
        $this->objectSettings()->dropTables();
    }


    /**
     *
     */
    public function installTables()/* : void*/
    {
        $this->objectSettings()->installTables();
    }


    /**
     * @return ObjectSettingsRepository
     */
    public function objectSettings() : ObjectSettingsRepository
    {
        return ObjectSettingsRepository::getInstance();
    }
}
