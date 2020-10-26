<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings;

use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class UserSettingsCtrl
 *
 * @package srag\Plugins\SrContainerObjectTree\UserSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserSettingsCtrl
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const CMD_EDIT_USER_SETTINGS = "editUserSettings";
    const CMD_UPDATE_USER_SETTINGS = "updateUserSettings";
    const LANG_MODULE = "user_settings";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var int
     */
    protected $tree_end_deep;
    /**
     * @var int
     */
    protected $tree_start_deep;
    /**
     * @var UserSettings
     */
    protected $user_settings;


    /**
     * UserSettingsCtrl constructor
     *
     * @param UserSettings $user_settings
     * @param int          $tree_start_deep
     * @param int          $tree_end_deep
     */
    public function __construct(UserSettings $user_settings, int $tree_start_deep, int $tree_end_deep)
    {
        $this->user_settings = $user_settings;
        $this->tree_start_deep = $tree_start_deep;
        $this->tree_end_deep = $tree_end_deep;
    }


    /**
     *
     */
    public function executeCommand()/* : void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_EDIT_USER_SETTINGS:
                    case self::CMD_UPDATE_USER_SETTINGS:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function editUserSettings()/* : void*/
    {
        $form = self::srContainerObjectTree()->userSettings()->factory()->newFormBuilderInstance(
            $this,
            $this->user_settings,
            $this->tree_start_deep,
            $this->tree_end_deep
        );

        self::output()->outputJSON([
            "html" => self::output()->getHTML($form)
        ]);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {

    }


    /**
     *
     */
    protected function updateUserSettings()/* : void*/
    {
        $form = self::srContainerObjectTree()->userSettings()->factory()->newFormBuilderInstance(
            $this,
            $this->user_settings,
            $this->tree_start_deep,
            $this->tree_end_deep
        );

        $ok = $form->storeForm();

        self::output()->outputJSON([
            "html" => self::output()->getHTML($form),
            "ok"   => $ok
        ]);
    }
}
