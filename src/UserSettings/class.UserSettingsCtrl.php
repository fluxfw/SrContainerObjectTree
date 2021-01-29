<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings;

require_once __DIR__ . "/../../vendor/autoload.php";

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
        $data = json_decode(file_get_contents("php://input"), true);
        $this->object->setShowMetadata(boolval($data["show_metadata"]));
        $this->object->setStartDeep(intval($data["start_deep"]));
        self::srContainerObjectTree()->userSettings()->storeUserSettings($this->object->getUserSettings());

        self::output()->outputJSON([
            "show_metadata" => $this->object->isShowMetadata(),
            "start_deep"    => $this->object->getStartDeep()
        ]);
    }
}
