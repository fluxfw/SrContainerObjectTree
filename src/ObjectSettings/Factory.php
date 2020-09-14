<?php

namespace srag\Plugins\SrContainerObjectTree\ObjectSettings;

use ilObjSrContainerObjectTree;
use ilObjSrContainerObjectTreeGUI;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\Form\FormBuilder;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrContainerObjectTree\ObjectSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
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
     * @param ilObjSrContainerObjectTreeGUI $parent
     * @param ilObjSrContainerObjectTree    $object
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(ilObjSrContainerObjectTreeGUI $parent, ilObjSrContainerObjectTree $object) : FormBuilder
    {
        $form = new FormBuilder($parent, $object);

        return $form;
    }


    /**
     * @return ObjectSettings
     */
    public function newInstance() : ObjectSettings
    {
        $object_settings = new ObjectSettings();

        return $object_settings;
    }
}
