<?php

namespace srag\Plugins\SrContainerObjectTree\Object;

use ilObject;
use ilObjectFactory;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\Object
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
     * @var string[][]
     */
    protected $container_object_types = [];
    /**
     * @var int[]
     */
    protected $deep = [];
    /**
     * @var bool[]
     */
    protected $has_read_access = [];
    /**
     * @var ilObject[]
     */
    protected $object_by_ref_id = [];
    /**
     * @var string[]
     */
    protected $object_type_titles = [];
    /**
     * @var string[][]
     */
    protected $object_types = [];
    /**
     * @var array[][]
     */
    protected $sub_tree = [];
    /**
     * @var array[][]
     */
    protected $sub_tree_children = [];


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
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param string[]|null $selected_object_types
     * @param bool          $only_types
     *
     * @return string[]
     */
    public function getContainerObjectTypes(/*?*/ array $selected_object_types = null, bool $only_types = true) : array
    {
        $cache_key = intval($only_types) . "_" . intval($selected_object_types !== null);

        if ($this->container_object_types[$cache_key] === null) {
            $this->container_object_types[$cache_key] = array_filter($this->getObjectTypes($selected_object_types, $only_types), [
                self::dic()->objDefinition(),
                "isContainer"
            ], (!$only_types ? ARRAY_FILTER_USE_KEY : 0));
        }

        return $this->container_object_types[$cache_key];
    }


    /**
     * @param int $ref_id
     *
     * @return int
     */
    public function getDeep(int $ref_id) : int
    {
        if ($this->deep[$ref_id] === null) {
            $this->deep[$ref_id] = self::dic()->repositoryTree()->getDepth($ref_id);
        }

        return $this->deep[$ref_id];
    }


    /**
     * @param int $obj_ref_id
     *
     * @return ilObject|null
     */
    public function getObjectByRefId(int $obj_ref_id)/* : ?ilObject*/
    {
        if ($this->object_by_ref_id[$obj_ref_id] === null) {
            $this->object_by_ref_id[$obj_ref_id] = ilObjectFactory::getInstanceByRefId($obj_ref_id, false);
        }

        return ($this->object_by_ref_id[$obj_ref_id] ?: null);
    }


    /**
     * @param string $type
     * @param bool   $multiple
     *
     * @return string
     */
    public function getObjectTypeTitle(string $type, bool $multiple = false) : string
    {
        $cache_key = $type . "_" . intval($multiple);

        if ($this->object_type_titles[$cache_key] === null) {
            $this->object_type_titles[$cache_key] = self::plugin()->translate("obj" . ($multiple ? "s" : "") . "_" . $type,
                (self::dic()->objDefinition()->isPluginTypeName($type) ? "rep_robj_" . $type : ""), [], false);
        }

        return $this->object_type_titles[$cache_key];
    }


    /**
     * @param string[]|null $selected_object_types
     * @param bool          $only_types
     *
     * @return string[]
     */
    public function getObjectTypes(/*?*/ array $selected_object_types = null, bool $only_types = true) : array
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
     * @param int $ref_id
     *
     * @return array[]
     */
    public function getSubTree(int $ref_id) : array
    {
        if ($this->sub_tree[$ref_id] === null) {
            $this->sub_tree[$ref_id] = array_reduce(self::dic()->repositoryTree()->getSubTree(self::dic()->repositoryTree()->getNodeData($ref_id), true),
                function (array $sub_tree, array $child) : array {
                    $sub_tree[$child["child"]] = $child;

                    $this->deep[$child["child"]] = $child["depth"];

                    return $sub_tree;
                }, []);
        }

        return $this->sub_tree[$ref_id];
    }


    /**
     * @param int $ref_id
     * @param int $parent_id
     *
     * @return array[]
     */
    public function getSubTreeChildren(int $ref_id, int $parent_id) : array
    {
        $cache_key = $ref_id . "_" . $parent_id;

        if ($this->sub_tree_children[$cache_key] === null) {
            $this->sub_tree_children[$cache_key] = array_filter($this->getSubTree($ref_id), function (array $child) use ($parent_id) : bool {
                return (intval($child["parent"]) === $parent_id);
            });

            uasort($this->sub_tree_children[$cache_key], function (array $child1, array $child2) : int {
                return strnatcasecmp($child1["title"], $child2["title"]);
            });
        }

        return $this->sub_tree_children[$cache_key];
    }


    /**
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasReadAccess(int $obj_ref_id) : bool
    {
        if ($this->has_read_access[$obj_ref_id] === null) {
            $this->has_read_access[$obj_ref_id] = self::dic()->access()->checkAccess("read", "", $obj_ref_id);
        }

        return $this->has_read_access[$obj_ref_id];
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {

    }
}
