<?php

namespace srag\Plugins\SrContainerObjectTree;

use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Config\Repository as ConfigRepository;
use srag\Plugins\SrContainerObjectTree\Object\Repository as ObjectsRepository;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\Repository as ObjectSettingsRepository;
use srag\Plugins\SrContainerObjectTree\Tree\Repository as TreeRepository;
use srag\Plugins\SrContainerObjectTree\UserSettings\Repository as UserSettingsRepository;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree
 */
final class Repository
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
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
     * @return ConfigRepository
     */
    public function config() : ConfigRepository
    {
        return ConfigRepository::getInstance();
    }


    /**
     *
     */
    public function dropTables() : void
    {
        $this->config()->dropTables();
        $this->objects()->dropTables();
        $this->objectSettings()->dropTables();
        $this->tree()->dropTables();
        $this->userSettings()->dropTables();
    }


    /**
     *
     */
    public function installTables() : void
    {
        $this->config()->installTables();
        $this->objects()->installTables();
        $this->objectSettings()->installTables();
        $this->tree()->installTables();
        $this->userSettings()->installTables();
    }


    /**
     * @return ObjectSettingsRepository
     */
    public function objectSettings() : ObjectSettingsRepository
    {
        return ObjectSettingsRepository::getInstance();
    }


    /**
     * @return ObjectsRepository
     */
    public function objects() : ObjectsRepository
    {
        return ObjectsRepository::getInstance();
    }


    /**
     * @return TreeRepository
     */
    public function tree() : TreeRepository
    {
        return TreeRepository::getInstance();
    }


    /**
     * @return UserSettingsRepository
     */
    public function userSettings() : UserSettingsRepository
    {
        return UserSettingsRepository::getInstance();
    }
}
