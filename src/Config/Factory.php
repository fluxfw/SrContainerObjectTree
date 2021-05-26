<?php

namespace srag\Plugins\SrContainerObjectTree\Config;

use ilSrContainerObjectTreePlugin;
use srag\ActiveRecordConfig\SrContainerObjectTree\Config\AbstractFactory;
use srag\Plugins\SrContainerObjectTree\Config\Form\FormBuilder;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrContainerObjectTree\Config
 */
final class Factory extends AbstractFactory
{

    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
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
     * @param ConfigCtrl $parent
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(ConfigCtrl $parent) : FormBuilder
    {
        $form = new FormBuilder($parent);

        return $form;
    }
}
