<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings;

use ilObjSrContainerObjectTree;
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

    const CMD_UPDATE_USER_SETTINGS = "updateUserSettings";
    const LANG_MODULE = "user_settings";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var ilObjSrContainerObjectTree
     */
    protected $object;


    /**
     * UserSettingsCtrl constructor
     *
     * @param ilObjSrContainerObjectTree $object
     */
    public function __construct(ilObjSrContainerObjectTree $object)
    {
        $this->object = $object;
    }


    /**
     * @return string
     */
    public function editUserSettings() : string
    {
        $form = self::srContainerObjectTree()->userSettings()->factory()->newFormBuilderInstance($this, $this->object);

        return self::output()->getHTML($form);
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
    protected function setTabs()/* : void*/
    {

    }


    /**
     *
     */
    protected function updateUserSettings()/* : void*/
    {
        $form = self::srContainerObjectTree()->userSettings()->factory()->newFormBuilderInstance($this, $this->object);

        $form->storeData();

        self::output()->outputJSON([
            "show_metadata" => $this->object->isShowMetadata(),
            "start_deep"    => $this->object->getStartDeep()
        ]);
    }
}
