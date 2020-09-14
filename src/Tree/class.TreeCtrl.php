<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

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
    /**
     * @var int
     */
    protected $container_ref_id;


    /**
     * TreeCtrl constructor
     *
     * @param int $container_ref_id
     */
    public function __construct(int $container_ref_id)
    {
        $this->container_ref_id = $container_ref_id;
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
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
        $ref_id = strval(filter_input(INPUT_GET, self::GET_PARAM_PARENT_REF_ID));

        $children = self::srContainerObjectTree()->tree()->getChildren($ref_id, true);

        self::output()->outputJSON($children);
    }


    /**
     *
     */
    protected function getHtml()/* : void*/
    {
        $html = self::srContainerObjectTree()
            ->tree()
            ->getHtml($this->container_ref_id, self::dic()->ctrl()->getLinkTarget($this, self::CMD_GET_CHILDREN, "", true) . "&" . self::GET_PARAM_PARENT_REF_ID);

        self::output()->output($html);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }
}
