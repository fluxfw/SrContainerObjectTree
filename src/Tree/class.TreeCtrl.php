<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

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
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectTree\UserSettings\UserSettingsCtrl: srag\Plugins\SrContainerObjectTree\Tree\TreeCtrl
 */
class TreeCtrl
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

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
    public function executeCommand()/* : void*/
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
    protected function getHtml()/* : void*/
    {
        $html = self::srContainerObjectTree()->tree()->getHtml($this->object);

        self::output()->output($html);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {

    }
}
