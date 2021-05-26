<?php

namespace srag\Plugins\SrContainerObjectTree\Config;

use ilSrContainerObjectTreePlugin;
use srag\ActiveRecordConfig\SrContainerObjectTree\Config\AbstractFactory;
use srag\ActiveRecordConfig\SrContainerObjectTree\Config\AbstractRepository;
use srag\ActiveRecordConfig\SrContainerObjectTree\Config\Config;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\Config
 */
final class Repository extends AbstractRepository
{

    use SrContainerObjectTreeTrait;

    const KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES = "allowed_empty_container_object_types";
    const KEY_LINK_CONTAINER_OBJECTS = "link_objects";
    const KEY_OBJECT_TYPES = "object_types";
    const KEY_OPEN_LINKS_IN_NEW_TAB = "open_links_in_new_tab";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;
    /**
     * @var string[]|null
     */
    protected $allowed_empty_container_object_types = null;
    /**
     * @var bool|null
     */
    protected $link_container_objects = null;
    /**
     * @var string[]|null
     */
    protected $object_types = null;
    /**
     * @var bool|null
     */
    protected $open_links_in_new_tab = null;


    /**
     * Repository constructor
     */
    protected function __construct()
    {
        parent::__construct();
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
     * @inheritDoc
     *
     * @return Factory
     */
    public function factory() : AbstractFactory
    {
        return Factory::getInstance();
    }


    /**
     * @return string[]
     */
    public function getAllowedEmptyContainerObjectTypes() : array
    {
        if ($this->allowed_empty_container_object_types === null) {
            $this->allowed_empty_container_object_types = $this->getValue(self::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES);
        }

        return $this->allowed_empty_container_object_types;
    }


    /**
     * @param string[] $allowed_empty_container_object_types
     */
    public function setAllowedEmptyContainerObjectTypes(array $allowed_empty_container_object_types)/* : void*/
    {
        $this->setValue(self::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES, $allowed_empty_container_object_types);
        $this->allowed_empty_container_object_types = $allowed_empty_container_object_types;
    }


    /**
     * @return string[]
     */
    public function getObjectTypes() : array
    {
        if ($this->object_types === null) {
            $this->object_types = $this->getValue(self::KEY_OBJECT_TYPES);
        }

        return $this->object_types;
    }


    /**
     * @param string[] $object_types
     */
    public function setObjectTypes(array $object_types)/* : void*/
    {
        $this->setValue(self::KEY_OBJECT_TYPES, $object_types);
        $this->object_types = $object_types;
    }


    /**
     * @return bool
     */
    public function isLinkContainerObjects() : bool
    {
        if ($this->link_container_objects === null) {
            $this->link_container_objects = $this->getValue(self::KEY_LINK_CONTAINER_OBJECTS);
        }

        return $this->link_container_objects;
    }


    /**
     * @return bool
     */
    public function isOpenLinksInNewTab() : bool
    {
        if ($this->open_links_in_new_tab === null) {
            $this->open_links_in_new_tab = $this->getValue(self::KEY_OPEN_LINKS_IN_NEW_TAB);
        }

        return $this->open_links_in_new_tab;
    }


    /**
     * @param bool $link_container_objects
     */
    public function setLinkContainerObjects(bool $link_container_objects)/* : void*/
    {
        $this->setValue(self::KEY_LINK_CONTAINER_OBJECTS, $link_container_objects);
        $this->link_container_objects = $link_container_objects;
    }


    /**
     * @param bool $open_links_in_new_tab
     */
    public function setOpenLinksInNewTab(bool $open_links_in_new_tab)/* : void*/
    {
        $this->setValue(self::KEY_OPEN_LINKS_IN_NEW_TAB, $open_links_in_new_tab);
        $this->open_links_in_new_tab = $open_links_in_new_tab;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        return [
            self::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES => [Config::TYPE_JSON, [], true],
            self::KEY_LINK_CONTAINER_OBJECTS               => [Config::TYPE_BOOLEAN, true],
            self::KEY_OBJECT_TYPES                         => [
                Config::TYPE_JSON,
                self::srContainerObjectTree()->objects()->getObjectTypes(),
                true
            ],
            self::KEY_OPEN_LINKS_IN_NEW_TAB                => [Config::TYPE_BOOLEAN, true]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function getTableName() : string
    {
        return ilSrContainerObjectTreePlugin::PLUGIN_ID . "_config";
    }
}
