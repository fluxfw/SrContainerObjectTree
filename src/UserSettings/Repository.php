<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings;

use ilDBConstants;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\UserSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
     * @var array
     */
    protected $user_settings = [];


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
     * @internal
     */
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(UserSettings::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int      $user_id
     * @param int|null $obj_id
     *
     * @return UserSettings
     */
    public function getUserSettings(int $user_id, /*?*/ int $obj_id = null) : UserSettings
    {
        $cache_key = $user_id . "_" . $obj_id;

        if ($this->user_settings[$cache_key] === null) {
            $this->user_settings[$cache_key] = UserSettings::where(["usr_id" => $user_id, "obj_id" => $obj_id])->first();

            if ($this->user_settings[$cache_key] === null) {
                $this->user_settings[$cache_key] = $this->factory()->newInstance();

                $this->user_settings[$cache_key]->setUsrId($user_id);

                $this->user_settings[$cache_key]->setObjId($obj_id);
            }
        }

        return $this->user_settings[$cache_key];
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        UserSettings::updateDB();

        self::dic()->database()->modifyTableColumn(UserSettings::TABLE_NAME, "obj_id", [
            "type"    => ilDBConstants::T_INTEGER,
            "length"  => 8,
            "notnull" => false
        ]);
    }


    /**
     *
     */
    public function resetUserSettings()/* : void*/
    {
        UserSettings::truncateDB();

        $this->user_settings = null;
    }


    /**
     * @param UserSettings $user_settings
     */
    public function storeUserSettings(UserSettings $user_settings)/* : void*/
    {
        $user_settings->store();
    }
}
