<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

require_once __DIR__ . "/../../vendor/autoload.php";

use ilObjSrContainerObjectTree;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class TreeCtrl
 *
 * @package           srag\Plugins\SrContainerObjectTree\Tree
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectTree\Tree\TreeCtrl: ilObjSrContainerObjectTreeGUI
 */
class TreeCtrl
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const CMD_FETCH_TREE = "fetchTree";
    const CMD_GET_HTML = "getHtml";
    const LANG_MODULE = "tree";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var ilObjSrContainerObjectTree
     */
    protected $object;


    /**
     * TreeCtrl constructor
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
    public function executeCommand() : void
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(UserSettingsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new UserSettingsCtrl($this->object));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd(self::CMD_GET_HTML);

                switch ($cmd) {
                    case self::CMD_FETCH_TREE:
                    case self::CMD_GET_HTML:
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
    protected function fetchTree() : void
    {
        $tree = self::srContainerObjectTree()->tree()->fetchTree($this->object);

        self::output()->outputJSON($tree);
    }


    /**
     *
     */
    protected function getHtml() : void
    {
        $html = self::srContainerObjectTree()
            ->tree()
            ->getHtml($this->object, self::dic()->ctrl()->getLinkTarget($this, self::CMD_FETCH_TREE, "", true),
                self::dic()->ctrl()->getFormActionByClass(UserSettingsCtrl::class, UserSettingsCtrl::CMD_UPDATE_USER_SETTINGS, "", true));

        self::output()->output($html);
    }


    /**
     *
     */
    protected function setTabs() : void
    {

    }
}
