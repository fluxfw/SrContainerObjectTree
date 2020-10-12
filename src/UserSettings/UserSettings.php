<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings;

use ActiveRecord;
use arConnector;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class UserSettings
 *
 * @package srag\Plugins\SrContainerObjectTree\UserSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserSettings extends ActiveRecord
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    const TABLE_NAME = ilSrContainerObjectTreePlugin::PLUGIN_ID . "_obj_usr";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $max_deep = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $obj_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $show_metadata = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $user_settings_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $usr_id;


    /**
     * UserSettings constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @param int $tree_end_deep
     *
     * @return int
     */
    public function getMaxDeep(int $tree_end_deep) : int
    {
        $max_deep = $this->max_deep;

        if ($max_deep === 0 || $max_deep > $tree_end_deep) {
            $max_deep = $tree_end_deep;
        }

        return $max_deep;
    }


    /**
     * @param int $max_deep
     */
    public function setMaxDeep(int $max_deep)/* : void*/
    {
        $this->max_deep = $max_deep;
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id)/* : void*/
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return int
     */
    public function getUserSettingsId() : int
    {
        return $this->user_settings_id;
    }


    /**
     * @param int $user_settings_id
     */
    public function setUserSettingsId(int $user_settings_id)/* : void*/
    {
        $this->user_settings_id = $user_settings_id;
    }


    /**
     * @return int
     */
    public function getUsrId() : int
    {
        return $this->usr_id;
    }


    /**
     * @param int $usr_id
     */
    public function setUsrId(int $usr_id)/* : void*/
    {
        $this->usr_id = $usr_id;
    }


    /**
     * @return bool
     */
    public function isShowDescription() : bool
    {
        $show_metadata = $this->show_metadata;

        if ($show_metadata === 0) {
            $show_metadata = 2;
        }

        return ($show_metadata === 2);
    }


    /**
     * @param bool $show_metadata
     */
    public function setShowMetadata(bool $show_metadata)/* : void*/
    {
        $this->show_metadata = ($show_metadata ? 2 : 1);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "max_deep":
            case "show_metadata":
                return intval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
