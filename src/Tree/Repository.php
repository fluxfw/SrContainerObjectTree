<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

use ilContainer;
use ilContainerSorting;
use ilLink;
use ilObject;
use ilObjectFactory;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Config\Form\FormBuilder;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\Tree
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    const CONTAINER_TYPES = ["cat", "crs", "fold", "grp", "root"];
    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @internal
     */
    public function dropTables()/* : void*/
    {

    }


    /**
     * @param int  $parent_ref_id
     * @param int  $parent_deep
     * @param int  $max_deep
     * @param bool $count_sub_children_types
     *
     * @return array
     */
    public function getChildren(int $parent_ref_id, int $parent_deep, int $max_deep, bool $count_sub_children_types = false) : array
    {
        $children = [];
        $current_deep = ($parent_deep + 1);

        $object = ilObjectFactory::getInstanceByRefId($parent_ref_id, false);

        if (
            !($object instanceof ilContainer)
            || !self::dic()->access()->checkAccess("read", "", $parent_ref_id)
            || ($max_deep !== 0 && $current_deep > $max_deep)
        ) {
            return [
                "children"     => $children,
                "current_deep" => $current_deep
            ];
        }

        $types = ilContainerSorting::_getInstance($object->getId())->getBlockPositions();
        if (empty($types)) {
            $types = array_reduce(self::dic()
                ->objDefinition()
                ->getGroupedRepositoryObjectTypes($object->getType()), function (array $types, array $type) : array {
                $types = array_merge($types, $type["objs"]);

                return $types;
            }, []);
        }

        $sub_items = $object->getSubItems();

        foreach ($types as $type) {
            foreach ((array) $sub_items[$type] as $sub_item) {
                if (!self::dic()->access()->checkAccess("read", "", $sub_item["child"])) {
                    continue;
                }

                $is_container = (in_array($sub_item["type"], self::CONTAINER_TYPES) && ($max_deep === 0 || $current_deep < $max_deep));

                $count_sub_children_types_count = ($is_container && $count_sub_children_types ? $this->getCountSubChildrenTypes($sub_item["child"], $current_deep, $max_deep) : []);

                if (self::srContainerObjectTree()->config()->getValue(FormBuilder::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY) && $is_container && empty($count_sub_children_types_count)) {
                    continue;
                }

                $children[] = [
                    "count_sub_children_types" => $count_sub_children_types_count,
                    "description"              => $sub_item["description"],
                    "icon"                     => ilObject::_getIcon($sub_item["obj_id"]),
                    "is_container"             => $is_container,
                    "link"                     => ilLink::_getLink($sub_item["child"]),
                    "ref_id"                   => $sub_item["child"],
                    "title"                    => $sub_item["title"],
                    "type"                     => $sub_item["type"]
                ];
            }
        }

        return [
            "children"     => $children,
            "current_deep" => $current_deep
        ];
    }


    /**
     * @param int    $tree_container_ref_id
     * @param string $tree_fetch_url
     * @param string $tree_edit_user_settings_url
     *
     * @return string
     */
    public function getHtml(int $tree_container_ref_id, string $tree_fetch_url, string $tree_edit_user_settings_url) : string
    {
        self::dic()->ui()->mainTemplate()->addCss(substr(self::plugin()->directory(), 2) . "/css/SrContainerObjectTree.css");
        self::dic()->ui()->mainTemplate()->addJavaScript(substr(self::plugin()->directory(), 2) . "/js/SrContainerObjectTree.min.js");

        $tpl = self::plugin()->template("SrContainerObjectTree.html");

        $config = [
            "edit_user_settings_error_text" => self::plugin()->translate("error", UserSettingsCtrl::LANG_MODULE),
            "edit_user_settings_fetch_url"  => $tree_edit_user_settings_url,
            "tree_container_ref_id"         => $tree_container_ref_id,
            "tree_empty_text"               => self::plugin()->translate("empty", TreeCtrl::LANG_MODULE),
            "tree_error_text"               => self::plugin()->translate("error", TreeCtrl::LANG_MODULE),
            "tree_fetch_url"                => $tree_fetch_url,
            "tree_link_objects"             => self::srContainerObjectTree()->config()->getValue(FormBuilder::KEY_LINK_OBJECTS)
        ];

        $tpl->setVariableEscaped("CONFIG", base64_encode(json_encode($config)));

        return self::output()->getHTML($tpl);
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {

    }


    /**
     * @param int $parent_ref_id
     * @param int $parent_deep
     * @param int $max_deep
     */
    protected function getCountSubChildrenTypes(int $parent_ref_id, int $parent_deep, int $max_deep) : array
    {
        return array_values(array_map(function (array $count_sub_children_type) : array {
            $count_sub_children_type["type_title"] = self::plugin()->translate("obj" . ($count_sub_children_type["count"] !== 1 ? "s" : "") . "_" . $count_sub_children_type["type"],
                (self::dic()->objDefinition()->isPluginTypeName($count_sub_children_type["type"]) ? "rep_robj_" . $count_sub_children_type["type"] : ""), [], false);

            return $count_sub_children_type;
        }, array_reduce($this->getChildren($parent_ref_id, $parent_deep, $max_deep, self::srContainerObjectTree()->config()->getValue(FormBuilder::KEY_RECURSIVE_COUNT))["children"],
            function (array $count_sub_children_types, array $children) : array {
                $count_sub_children_types = $this->getCountSubChildrenTypesCount($count_sub_children_types, $children["type"]);

                if (self::srContainerObjectTree()->config()->getValue(FormBuilder::KEY_RECURSIVE_COUNT)) {
                    foreach ($children["count_sub_children_types"] as $children2) {
                        $count_sub_children_types = $this->getCountSubChildrenTypesCount($count_sub_children_types, $children2["type"], $children2["count"]);
                    }
                }

                return $count_sub_children_types;
            }, [])));
    }


    /**
     * @param array  $count_sub_children_types
     * @param string $type
     * @param int    $additional_count
     *
     * @return array
     */
    protected function getCountSubChildrenTypesCount(array $count_sub_children_types, string $type, int $additional_count = 1) : array
    {
        if (!isset($count_sub_children_types[$type])) {
            $count_sub_children_types[$type] = [
                "count" => 0,
                "type"  => $type
            ];
        }

        $count_sub_children_types[$type]["count"] += $additional_count;

        return $count_sub_children_types;
    }
}
