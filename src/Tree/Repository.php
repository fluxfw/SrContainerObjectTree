<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

use ilContainer;
use ilContainerSorting;
use ilLink;
use ilObject;
use ilObjectFactory;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
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

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;
    /**
     * @var array|null
     */
    protected $container_object_types = null;
    /**
     * @var array
     */
    protected $object_type_titles = [];
    /**
     * @var array
     */
    protected $object_types = [];


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
     * @param int   $parent_ref_id
     * @param int   $parent_deep
     * @param int   $max_deep
     * @param array $object_types
     * @param bool  $only_show_container_objects_if_not_empty
     * @param bool  $recursive_count
     * @param bool  $count_sub_children_types
     *
     * @return array
     */
    public function getChildren(
        int $parent_ref_id,
        int $parent_deep,
        int $max_deep,
        array $object_types,
        bool $only_show_container_objects_if_not_empty,
        bool $recursive_count,
        bool $count_sub_children_types = true
    ) : array {
        $children = [];
        $current_deep = ($parent_deep + 1);

        $object = ilObjectFactory::getInstanceByRefId($parent_ref_id, false);

        if (!in_array($object->getType(), $this->getContainerObjectTypes($object_types))
            || !($object instanceof ilContainer)
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
            $types = array_reduce(self::dic()->objDefinition()->getGroupedRepositoryObjectTypes($object->getType()), function (array $types, array $type) : array {
                $types = array_merge($types, $type["objs"]);

                return $types;
            }, []);
        }

        $sub_items = $object->getSubItems();

        foreach ($types as $type) {
            foreach ((array) $sub_items[$type] as $sub_item) {
                if (!in_array($sub_item["type"], $this->getObjectTypes($object_types))
                    || !self::dic()->access()->checkAccess("read", "", $sub_item["child"])
                ) {
                    continue;
                }

                $is_container = (in_array($sub_item["type"], $this->getContainerObjectTypes($object_types))
                    && ($max_deep === 0 || $current_deep < $max_deep));

                $count_sub_children_types_count = ($is_container
                && $count_sub_children_types ? $this->getCountSubChildrenTypes(
                    $sub_item["child"],
                    $current_deep,
                    $max_deep,
                    $object_types,
                    $only_show_container_objects_if_not_empty,
                    $recursive_count
                ) : []);

                if ($only_show_container_objects_if_not_empty
                    && $is_container
                    && empty($count_sub_children_types_count)
                ) {
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
     * @param array|null $selected_object_types
     *
     * @return array
     */
    public function getContainerObjectTypes( /*?*/ array $selected_object_types = null) : array
    {
        if ($this->container_object_types === null) {
            $this->container_object_types = array_filter($this->getObjectTypes($selected_object_types), [
                self::dic()->objDefinition(),
                "isContainer"
            ]);
        }

        return $this->container_object_types;
    }


    /**
     * @param int    $tree_container_ref_id
     * @param bool   $tree_link_objects
     * @param string $tree_fetch_url
     * @param string $tree_empty_text
     * @param string $tree_error_text
     * @param string $edit_user_settings_url
     * @param string $edit_user_settings_error_text
     *
     * @return string
     */
    public function getHtml(
        int $tree_container_ref_id,
        bool $tree_link_objects,
        string $tree_fetch_url,
        string $tree_empty_text,
        string $tree_error_text,
        string $edit_user_settings_url,
        string $edit_user_settings_error_text
    ) : string {
        self::dic()->ui()->mainTemplate()->addCss(substr(self::plugin()->directory(), 2) . "/css/SrContainerObjectTree.css");
        self::dic()->ui()->mainTemplate()->addJavaScript(substr(self::plugin()->directory(), 2) . "/js/SrContainerObjectTree.min.js");

        $tpl = self::plugin()->template("SrContainerObjectTree.html");

        $config = [
            "edit_user_settings_error_text" => $edit_user_settings_error_text,
            "edit_user_settings_fetch_url"  => $edit_user_settings_url,
            "tree_container_ref_id"         => $tree_container_ref_id,
            "tree_empty_text"               => $tree_empty_text,
            "tree_error_text"               => $tree_error_text,
            "tree_fetch_url"                => $tree_fetch_url,
            "tree_link_objects"             => $tree_link_objects
        ];

        $tpl->setVariableEscaped("CONFIG", base64_encode(json_encode($config)));

        return self::output()->getHTML($tpl);
    }


    /**
     * @param string $type
     * @param bool   $multiple
     *
     * @return string
     */
    public function getObjectTypeTitle(string $type, bool $multiple = false) : string
    {
        if ($this->object_type_titles[$type . "_" . $multiple] === null) {
            $this->object_type_titles[$type . "_" . $multiple] = self::plugin()->translate("obj" . ($multiple ? "s" : "") . "_" . $type,
                (self::dic()->objDefinition()->isPluginTypeName($type) ? "rep_robj_" . $type : ""), [], false);
        }

        return $this->object_type_titles[$type . "_" . $multiple];
    }


    /**
     * @param array|null $selected_object_types
     * @param bool       $only_types
     *
     * @return array
     */
    public function getObjectTypes( /*?*/ array $selected_object_types = null, bool $only_types = true) : array
    {
        $cache_key = intval($only_types) . "_" . intval($selected_object_types !== null);

        if ($this->object_types[$cache_key] === null) {
            if ($only_types) {
                $this->object_types[$cache_key] = array_keys($this->getObjectTypes($selected_object_types, false));
            } else {
                if ($selected_object_types !== null) {
                    $this->object_types[$cache_key] = array_filter($this->getObjectTypes(null, $only_types), function (string $type) use ($selected_object_types) : bool {
                        return in_array($type, $selected_object_types);
                    }, ARRAY_FILTER_USE_KEY);
                } else {
                    $this->object_types[$cache_key] = array_reduce(array_filter(self::dic()->objDefinition()->getAllObjects(), function (string $type) use ($selected_object_types) : bool {
                        return (self::dic()->objDefinition()->isAllowedInRepository($type)
                            && self::dic()->objDefinition()->isRBACObject($type)
                            && !self::dic()->objDefinition()->isAdministrationObject($type)
                            && ($type === "root" || !self::dic()->objDefinition()->isSystemObject($type)));
                    }), function (array $object_types, string $type) : array {
                        $object_types[$type] = $this->getObjectTypeTitle($type);

                        return $object_types;
                    }, []);
                }
            }

            uasort($this->object_types[$cache_key], "strnatcasecmp");
        }

        return $this->object_types[$cache_key];
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {

    }


    /**
     * @param int   $parent_ref_id
     * @param int   $parent_deep
     * @param int   $max_deep
     * @param array $object_types
     * @param bool  $only_show_container_objects_if_not_empty
     * @param bool  $recursive_count
     *
     * @return array
     */
    protected function getCountSubChildrenTypes(
        int $parent_ref_id,
        int $parent_deep,
        int $max_deep,
        array $object_types,
        bool $only_show_container_objects_if_not_empty,
        bool $recursive_count
    ) : array {
        return array_values(array_map(function (array $count_sub_children_type) : array {
            $count_sub_children_type["type_title"] = $this->getObjectTypeTitle($count_sub_children_type["type"], ($count_sub_children_type["count"] !== 1));

            return $count_sub_children_type;
        }, array_reduce($this->getChildren(
            $parent_ref_id,
            $parent_deep,
            $max_deep,
            $object_types,
            $only_show_container_objects_if_not_empty,
            $recursive_count,
            $recursive_count
        )["children"],
            function (array $count_sub_children_types, array $children) use ($recursive_count) : array {
                $count_sub_children_types = $this->getCountSubChildrenTypesCount($count_sub_children_types, $children["type"]);

                if ($recursive_count) {
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
