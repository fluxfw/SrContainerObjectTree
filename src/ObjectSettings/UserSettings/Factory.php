<?php

namespace srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings;

use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\Form\FormBuilder;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings
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
     * @param UserSettingsCtrl $parent
     * @param UserSettings     $user_settings
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(UserSettingsCtrl $parent, UserSettings $user_settings) : FormBuilder
    {
        $form = new FormBuilder($parent, $user_settings);

        return $form;
    }


    /**
     * @return UserSettings
     */
    public function newInstance() : UserSettings
    {
        $settings = new UserSettings();

        return $settings;
    }
}
