<?php

namespace srag\Plugins\SrContainerObjectTree\ObjectSettings;

use ActiveRecord;
use arConnector;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class ObjectSettings
 *
 * @package srag\Plugins\SrContainerObjectTree\ObjectSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjectSettings extends ActiveRecord
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    const TABLE_NAME = "rep_robj_" . ilSrContainerObjectTreePlugin::PLUGIN_ID . "_obj";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $container_ref_id = 0;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $is_online = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $obj_id;


    /**
     * ObjectSettings constructor
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
     * @return int
     */
    public function getContainerRefId() : int
    {
        return $this->container_ref_id;
    }


    /**
     * @param int $container_ref_id
     */
    public function setContainerRefId(int $container_ref_id)/* : void*/
    {
        $this->container_ref_id = $container_ref_id;
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
     * @return bool
     */
    public function isOnline() : bool
    {
        return $this->is_online;
    }


    /**
     * @param bool $is_online
     */
    public function setOnline(bool $is_online = true)/* : void*/
    {
        $this->is_online = $is_online;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "is_online":
                return ($field_value ? 1 : 0);

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
            case "container_ref_id":
            case "obj_id":
                return intval($field_value);

            case "is_online":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
