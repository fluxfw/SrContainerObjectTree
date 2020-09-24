<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

use ilObjSrContainerObjectTree;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class TreeCtrl
 *
 * @package           srag\Plugins\SrContainerObjectTree\Tree
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\UserSettingsCtrl: srag\Plugins\SrContainerObjectTree\Tree\TreeCtrl
 */
class TreeCtrl
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const CMD_GET_CHILDREN = "getChildren";
    const CMD_GET_HTML = "getHtml";
    const GET_PARAM_PARENT_DEEP = "parent_deep";
    const GET_PARAM_PARENT_REF_ID = "parent_ref_id";
    const LANG_MODULE = "tree";
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
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(UserSettingsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new UserSettingsCtrl($this->object->getUserSettings()));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd(self::CMD_GET_HTML);

                switch ($cmd) {
                    case self::CMD_GET_CHILDREN:
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
    protected function getChildren()/* : void*/
    {
        $parent_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_PARENT_REF_ID));
        $parent_deep = intval(filter_input(INPUT_GET, self::GET_PARAM_PARENT_DEEP));

        $children = self::srContainerObjectTree()->tree()->getChildren($parent_ref_id, $parent_deep, $this->object->getUserSettings()->getMaxDeep(), true);

        self::output()->outputJSON($children);
    }


    /**
     *
     */
    protected function getHtml()/* : void*/
    {
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_PARENT_REF_ID, ":parent_ref_id");
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_PARENT_DEEP, ":parent_deep");

        $html = self::srContainerObjectTree()
            ->tree()
            ->getHtml($this->object->getContainerRefId(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_GET_CHILDREN, "", true),
                self::dic()->ctrl()->getLinkTargetByClass(UserSettingsCtrl::class, UserSettingsCtrl::CMD_EDIT_USER_SETTINGS, "", true));

        self::output()->output($html);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }
}
