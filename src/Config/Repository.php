<?php

namespace srag\Plugins\SrContainerObjectTree\Config;

use ilSrContainerObjectTreePlugin;
use srag\ActiveRecordConfig\SrContainerObjectTree\Config\AbstractFactory;
use srag\ActiveRecordConfig\SrContainerObjectTree\Config\AbstractRepository;
use srag\ActiveRecordConfig\SrContainerObjectTree\Config\Config;
use srag\Plugins\SrContainerObjectTree\Config\Form\FormBuilder;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository extends AbstractRepository
{

    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


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
     * @inheritDoc
     */
    protected function getFields() : array
    {
        return [
            FormBuilder::KEY_LINK_OBJECTS                             => [Config::TYPE_BOOLEAN, true],
            FormBuilder::KEY_OBJECT_TYPES                             => [Config::TYPE_JSON, self::srContainerObjectTree()->tree()->getObjectTypes(), true],
            FormBuilder::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY => [Config::TYPE_BOOLEAN, false],
            FormBuilder::KEY_RECURSIVE_COUNT                          => [Config::TYPE_BOOLEAN, false]
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
