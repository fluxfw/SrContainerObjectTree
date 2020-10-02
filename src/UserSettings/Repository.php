<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings;

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
     * @param int $obj_id
     * @param int $user_id
     *
     * @return UserSettings
     */
    public function getUserSettings(int $obj_id, int $user_id) : UserSettings
    {
        /**
         * @var UserSettings|null $settings
         */

        $settings = UserSettings::where(["obj_id" => $obj_id, "usr_id" => $user_id])->first();

        if ($settings === null) {
            $settings = $this->factory()->newInstance();

            $settings->setObjId($obj_id);

            $settings->setUsrId($user_id);
        }

        return $settings;
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        UserSettings::updateDB();
    }


    /**
     * @param UserSettings $user_settings
     */
    public function storeUserSettings(UserSettings $user_settings)/* : void*/
    {
        $user_settings->store();
    }
}
