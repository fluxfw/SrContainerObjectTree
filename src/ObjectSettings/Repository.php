<?php

namespace srag\Plugins\SrContainerObjectTree\ObjectSettings;

use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\ObjectSettings
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
     * @var ObjectSettings[]
     */
    protected $object_settings_by_id = [];


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
     * @param ObjectSettings $object_settings
     *
     * @return ObjectSettings
     */
    public function cloneObjectSettings(ObjectSettings $object_settings) : ObjectSettings
    {
        return $object_settings->copy();
    }


    /**
     * @param ObjectSettings $object_settings
     */
    public function deleteObjectSettings(ObjectSettings $object_settings) : void
    {
        $object_settings->delete();

        foreach (self::srContainerObjectTree()->userSettings()->getUserSettingsByObjId($object_settings->getObjId()) as $user_settings) {
            self::srContainerObjectTree()->userSettings()->deleteUserSettings($user_settings);
        }

        unset($this->object_settings_by_id[$object_settings->getObjId()]);
    }


    /**
     * @internal
     */
    public function dropTables() : void
    {
        self::dic()->database()->dropTable(ObjectSettings::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_id
     *
     * @return ObjectSettings|null
     */
    public function getObjectSettingsById(int $obj_id) : ?ObjectSettings
    {
        if ($this->object_settings_by_id[$obj_id] === null) {
            $this->object_settings_by_id[$obj_id] = ObjectSettings::where([
                "obj_id" => $obj_id
            ])->first();
        }

        return $this->object_settings_by_id[$obj_id];
    }


    /**
     * @internal
     */
    public function installTables() : void
    {
        ObjectSettings::updateDB();
    }


    /**
     * @param ObjectSettings $object_settings
     */
    public function storeObjectSettings(ObjectSettings $object_settings) : void
    {
        $object_settings->store();

        $this->object_settings_by_id[$object_settings->getObjId()] = $object_settings;
    }
}
