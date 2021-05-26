<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings;

use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\UserSettings
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
     * @var UserSettings[][]
     */
    protected $user_settings_by_obj_id = [];
    /**
     * @var UserSettings[]
     */
    protected $user_settings_by_user_id_and_obj_id = [];


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
     * @param UserSettings $user_settings
     */
    public function deleteUserSettings(UserSettings $user_settings)/* : void*/
    {
        $user_settings->delete();

        unset($this->user_settings_by_user_id_and_obj_id[$user_settings->getUsrId() . "_" . $user_settings->getObjId()]);
        $this->user_settings_by_obj_id = [];
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
     *
     * @return UserSettings[]
     */
    public function getUserSettingsByObjId(int $obj_id) : array
    {
        if ($this->user_settings_by_obj_id[$obj_id] === null) {
            $this->user_settings_by_obj_id[$obj_id] = array_values(UserSettings::where(["obj_id" => $obj_id])->get());

            foreach ($this->user_settings_by_obj_id[$obj_id] as $user_settings) {
                $this->user_settings_by_user_id_and_obj_id[$user_settings->getUsrId() . "_" . $user_settings->getObjId()] = $user_settings;
            }
        }

        return $this->user_settings_by_obj_id[$obj_id];
    }


    /**
     * @param int $user_id
     * @param int $obj_id
     *
     * @return UserSettings
     */
    public function getUserSettingsByUserIdAndObjId(int $user_id, int $obj_id) : UserSettings
    {
        $cache_key = $user_id . "_" . $obj_id;

        if ($this->user_settings_by_user_id_and_obj_id[$cache_key] === null) {
            $this->user_settings_by_user_id_and_obj_id[$cache_key] = UserSettings::where(["usr_id" => $user_id, "obj_id" => $obj_id])->first();

            if ($this->user_settings_by_user_id_and_obj_id[$cache_key] === null) {
                $this->user_settings_by_user_id_and_obj_id[$cache_key] = $this->factory()->newInstance();

                $this->user_settings_by_user_id_and_obj_id[$cache_key]->setUsrId($user_id);

                $this->user_settings_by_user_id_and_obj_id[$cache_key]->setObjId($obj_id);
            }
        }

        return $this->user_settings_by_user_id_and_obj_id[$cache_key];
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

        $this->user_settings_by_user_id_and_obj_id[$user_settings->getUsrId() . "_" . $user_settings->getObjId()] = $user_settings;
        $this->user_settings_by_obj_id = [];
    }
}
