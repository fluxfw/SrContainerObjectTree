<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

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
    const GET_PARAM_PARENT_DEEP = "parent_deep";
    const GET_PARAM_PARENT_REF_ID = "parent_ref_id";
    const LANG_MODULE = "tree";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var int
     */
    protected $container_ref_id;
    /**
     * @var string
     */
    protected $edit_user_settings_error_text;
    /**
     * @var string
     */
    protected $edit_user_settings_url;
    /**
     * @var bool
     */
    protected $link_container_objects;
    /**
     * @var int
     */
    protected $max_deep;
    /**
     * @var int
     */
    protected $max_deep_method;
    /**
     * @var bool
     */
    protected $max_deep_method_start_hide;
    /**
     * @var array
     */
    protected $object_types;
    /**
     * @var bool
     */
    protected $only_show_container_objects_if_not_empty;
    /**
     * @var bool
     */
    protected $open_links_in_new_tab;
    /**
     * @var bool
     */
    protected $recursive_count;
    /**
     * @var bool
     */
    protected $show_metadata;


    /**
     * TreeCtrl constructor
     *
     * @param int    $container_ref_id
     * @param string $edit_user_settings_url
     * @param string $edit_user_settings_error_text
     * @param bool   $link_container_objects
     * @param int    $max_deep
     * @param int    $max_deep_method
     * @param bool   $max_deep_method_start_hide
     * @param array  $object_types
     * @param bool   $only_show_container_objects_if_not_empty
     * @param bool   $open_links_in_new_tab
     * @param bool   $recursive_count
     * @param bool   $show_metadata
     */
    public function __construct(
        int $container_ref_id,
        string $edit_user_settings_url,
        string $edit_user_settings_error_text,
        bool $link_container_objects,
        int $max_deep,
        int $max_deep_method,
        bool $max_deep_method_start_hide,
        array $object_types,
        bool $only_show_container_objects_if_not_empty,
        bool $open_links_in_new_tab,
        bool $recursive_count,
        bool $show_metadata
    ) {
        $this->container_ref_id = $container_ref_id;
        $this->edit_user_settings_url = $edit_user_settings_url;
        $this->edit_user_settings_error_text = $edit_user_settings_error_text;
        $this->link_container_objects = $link_container_objects;
        $this->max_deep = $max_deep;
        $this->max_deep_method = $max_deep_method;
        $this->max_deep_method_start_hide = $max_deep_method_start_hide;
        $this->object_types = $object_types;
        $this->only_show_container_objects_if_not_empty = $only_show_container_objects_if_not_empty;
        $this->open_links_in_new_tab = $open_links_in_new_tab;
        $this->recursive_count = $recursive_count;
        $this->show_metadata = $show_metadata;
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
        $parent_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_PARENT_REF_ID));
        $parent_deep = intval(filter_input(INPUT_GET, self::GET_PARAM_PARENT_DEEP));

        $children = self::srContainerObjectTree()->tree()->getChildren(
            $parent_ref_id,
            $parent_deep,
            $this->link_container_objects,
            $this->max_deep,
            $this->max_deep_method,
            $this->max_deep_method_start_hide,
            $this->object_types,
            $this->only_show_container_objects_if_not_empty,
            $this->open_links_in_new_tab,
            $this->recursive_count,
            $this->show_metadata
        );

        self::output()->outputJSON($children);
    }


    /**
     *
     */
    protected function getHtml()/* : void*/
    {
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_PARENT_REF_ID, ":parent_ref_id");
        self::dic()->ctrl()->setParameter($this, self::GET_PARAM_PARENT_DEEP, ":parent_deep");

        $html = self::srContainerObjectTree()->tree()->getHtml(
            $this->container_ref_id,
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_GET_CHILDREN, "", true),
            self::plugin()->translate("empty", self::LANG_MODULE),
            self::plugin()->translate("error", self::LANG_MODULE),
            $this->edit_user_settings_url,
            $this->edit_user_settings_error_text
        );

        self::output()->output($html);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {

    }
}
