<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

use ilObjSrContainerObjectTree;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class TreeCtrl
 *
 * @package srag\Plugins\SrContainerObjectTree\Tree
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TreeCtrl
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const CMD_GET_CHILDREN = "getChildren";
    const CMD_GET_HTML = "getHtml";
    const GET_PARAM_PARENT_REF_ID = "parent_ref_id";
    const LANG_MODULE = "tree";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var string
     */
    protected $edit_user_settings_error_text;
    /**
     * @var string
     */
    protected $edit_user_settings_url;
    /**
     * @var ilObjSrContainerObjectTree
     */
    protected $object;


    /**
     * TreeCtrl constructor
     *
     * @param string                     $edit_user_settings_url
     * @param string                     $edit_user_settings_error_text
     * @param ilObjSrContainerObjectTree $object
     */
    public function __construct(string $edit_user_settings_url, string $edit_user_settings_error_text, ilObjSrContainerObjectTree $object)
    {
        $this->edit_user_settings_url = $edit_user_settings_url;
        $this->edit_user_settings_error_text = $edit_user_settings_error_text;
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

        $children = self::srContainerObjectTree()->tree()->getChildren($parent_ref_id, $this->object);

        self::output()->outputJSON($children);
    }


    /**
     *
     */
    protected function getHtml()/* : void*/
    {
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_PARENT_REF_ID, ":parent_ref_id");

        $html = self::srContainerObjectTree()
            ->tree()
            ->getHtml($this->object, self::dic()->ctrl()->getLinkTarget($this, self::CMD_GET_CHILDREN, "", true), self::plugin()->translate("empty", self::LANG_MODULE),
                self::plugin()->translate("error", self::LANG_MODULE), $this->edit_user_settings_url, $this->edit_user_settings_error_text);

        self::output()->output($html);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {

    }
}
