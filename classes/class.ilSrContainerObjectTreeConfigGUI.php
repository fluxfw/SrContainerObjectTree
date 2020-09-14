<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrContainerObjectTree\DevTools\DevToolsCtrl;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class ilSrContainerObjectTreeConfigGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\DIC\SrContainerObjectTree\DevTools\DevToolsCtrl: ilSrContainerObjectTreeConfigGUI
 */
class ilSrContainerObjectTreeConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const CMD_CONFIGURE = "configure";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;


    /**
     * ilSrContainerObjectTreeConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/*:void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(DevToolsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new DevToolsCtrl($this, self::plugin()));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
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
    protected function configure()/*: void*/
    {
        self::dic()->ctrl()->redirectByClass(DevToolsCtrl::class);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        DevToolsCtrl::addTabs(self::plugin());

        self::dic()->locator()->addItem(ilSrContainerObjectTreePlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }
}
