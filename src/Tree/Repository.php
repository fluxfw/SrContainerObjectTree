<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

use ilContainer;
use ilLink;
use ilObject;
use ilObjSrContainerObjectTree;
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
     * @var array[][]
     */
    protected $children = [];
    /**
     * @var int[]
     */
    protected $default_deep = [];
    /**
     * @var int[]
     */
    protected $max_deep = [];
    /**
     * @var int[]
     */
    protected $min_deep = [];
    /**
     * @var int[]
     */
    protected $start_deep = [];


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
     * @param int                        $parent_ref_id
     * @param ilObjSrContainerObjectTree $object
     *
     * @return array[]
     */
    public function getChildren(int $parent_ref_id, ilObjSrContainerObjectTree $object) : array
    {
        $cache_key = $object->getId() . "_" . $parent_ref_id;

        if ($this->children[$cache_key] === null) {
            $children = [];
            $current_deep = (self::srContainerObjectTree()->objects()->getDeep($parent_ref_id) - self::srContainerObjectTree()->objects()->getDeep($object->getContainerRefId()) + 1);

            $parent_object = self::srContainerObjectTree()->objects()->getObjectByRefId($parent_ref_id);

            if ($parent_object === null
                || !in_array($parent_object->getType(), self::srContainerObjectTree()->objects()->getContainerObjectTypes(self::srContainerObjectTree()->config()->getObjectTypes()))
                || !($parent_object instanceof ilContainer)
                || !self::srContainerObjectTree()->objects()->hasReadAccess($parent_ref_id)
            ) {
                return [
                    "children" => $children
                ];
            }

            foreach (self::srContainerObjectTree()->objects()->getSubTreeChildren($parent_object->getRefId(), $parent_ref_id) as $sub_item) {
                $ref_id = $sub_item["child"];
                $type = $sub_item["type"];

                if (!in_array($type, self::srContainerObjectTree()->objects()->getObjectTypes(self::srContainerObjectTree()->config()->getObjectTypes()))
                    || !self::srContainerObjectTree()->objects()->hasReadAccess($ref_id)
                ) {
                    continue;
                }

                $is_container = in_array($type, self::srContainerObjectTree()->objects()->getContainerObjectTypes(self::srContainerObjectTree()->config()->getObjectTypes()));

                $count_sub_children_types_count = ($is_container ? $this->getCountSubChildrenTypes($ref_id, $object) : []);

                if ($is_container && empty($count_sub_children_types_count)) {
                    if (in_array($type, self::srContainerObjectTree()->config()->getAllowedEmptyContainerObjectTypes())) {
                        $is_container = false;
                    } else {
                        continue;
                    }
                }

                if ($object->isShowMetadata()) {
                    $description = $sub_item["description"];
                } else {
                    $description = null;
                    $count_sub_children_types_count = [];
                }

                if (self::srContainerObjectTree()->config()->isLinkContainerObjects() || !$is_container) {
                    $link = ilLink::_getLink($ref_id);
                } else {
                    $link = null;
                }

                $preloaded_children = $this->getChildren($ref_id, $object);

                $pre_open = ($is_container && $current_deep < $this->getStartDeep($object));

                $child = [
                    "count_sub_children_types" => $count_sub_children_types_count,
                    "description"              => $description,
                    "icon"                     => ilObject::_getIcon($sub_item["obj_id"]),
                    "is_container"             => $is_container,
                    "link"                     => $link,
                    "link_new_tab"             => self::srContainerObjectTree()->config()->isOpenLinksInNewTab(),
                    "preloaded_children"       => $preloaded_children,
                    "pre_open"                 => $pre_open,
                    "ref_id"                   => $ref_id,
                    "title"                    => $sub_item["title"],
                    "type"                     => $type
                ];

                self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrContainerObjectTreePlugin::PLUGIN_NAME, ilSrContainerObjectTreePlugin::EVENT_CHANGE_CHILD_BEFORE_OUTPUT, [
                    "child" => &$child // Unfortunately ILIAS Raise Event System not supports return results so use a referenced variable
                ]);

                $children[] = $child;
            }

            $this->children[$cache_key] = [
                "children" => $children
            ];
        }

        return $this->children[$cache_key];
    }


    /**
     * @param ilObjSrContainerObjectTree $object
     *
     * @return int
     */
    public function getDefaultDeep(ilObjSrContainerObjectTree $object) : int
    {
        if ($this->default_deep[$object->getId()] === null) {
            $children = self::srContainerObjectTree()->objects()->getSubTree($object->getContainerRefId());

            $ref_id_deep = $default_deep = $children[$object->getContainerRefId()]["depth"];

            foreach ($children as $child) {
                if (count($this->getChildren($child["child"], $object)["children"]) > 1) {
                    $default_deep = $child["depth"];
                    break;
                }
            }

            $this->default_deep[$object->getId()] = ($default_deep - $ref_id_deep + 1);
        }

        return $this->default_deep[$object->getId()];
    }


    /**
     * @param ilObjSrContainerObjectTree $object
     * @param string                     $tree_fetch_url
     * @param string                     $tree_empty_text
     * @param string                     $tree_error_text
     * @param string                     $edit_user_settings_url
     * @param string                     $edit_user_settings_error_text
     *
     * @return string
     */
    public function getHtml(
        ilObjSrContainerObjectTree $object,
        string $tree_fetch_url,
        string $tree_empty_text,
        string $tree_error_text,
        string $edit_user_settings_url,
        string $edit_user_settings_error_text
    ) : string {
        if (self::version()->is6()) {
            $glyph_factory = self::dic()->ui()->factory()->symbol()->glyph();
        } else {
            $glyph_factory = self::dic()->ui()->factory()->glyph();
        }

        self::dic()->ui()->mainTemplate()->addCss(substr(self::plugin()->directory(), 2) . "/css/SrContainerObjectTree.css");
        self::dic()->ui()->mainTemplate()->addJavaScript(substr(self::plugin()->directory(), 2) . "/js/SrContainerObjectTree.min.js");

        $tpl = self::plugin()->template("SrContainerObjectTree.html");
        $tpl_tree = self::plugin()->template("SrContainerObjectTreeTree.html", true, false);
        $tpl_user_settings = self::plugin()->template("SrContainerObjectTreeEditUserSettings.html", true, false);

        $config = [
            "edit_user_settings_error_text" => $edit_user_settings_error_text,
            "edit_user_settings_fetch_url"  => $edit_user_settings_url,
            "tree_container_ref_id"         => $object->getContainerRefId(),
            "tree_empty_text"               => $tree_empty_text,
            "tree_error_text"               => $tree_error_text,
            "tree_fetch_url"                => $tree_fetch_url
        ];

        $tpl->setVariableEscaped("CONFIG", base64_encode(json_encode($config)));

        $tpl->setVariable("TREE", self::output()->getHTML($tpl_tree));

        $tpl_user_settings_icon = self::plugin()->template("SrContainerObjectTreeEditUserSettingsIcon.html");
        $tpl_user_settings_icon->setVariable("USER_SETTINGS_ICON", self::output()->getHTML($glyph_factory->settings()));
        $tpl_user_settings->setVariable("USER_SETTINGS_ICON", self::output()->getHTML($tpl_user_settings_icon));
        $tpl->setVariable("USER_SETTINGS", self::output()->getHTML($tpl_user_settings));

        return self::output()->getHTML($tpl);
    }


    /**
     * @param ilObjSrContainerObjectTree $object
     *
     * @return int
     */
    public function getMaxDeep(ilObjSrContainerObjectTree $object) : int
    {
        if ($this->max_deep[$object->getId()] === null) {
            $children = self::srContainerObjectTree()->objects()->getSubTree($object->getContainerRefId());

            $ref_id_deep = $max_deep = $children[$object->getContainerRefId()]["depth"];

            foreach ($children as $child) {
                if ($child["depth"] <= $max_deep) {
                    continue;
                }

                if (!empty($this->getChildren($child["child"], $object)["children"])) {
                    $max_deep = max($max_deep, $child["depth"]);
                }
            }

            $this->max_deep[$object->getId()] = ($max_deep - $ref_id_deep + 1);
        }

        return $this->max_deep[$object->getId()];
    }


    /**
     * @param ilObjSrContainerObjectTree $object
     *
     * @return int
     */
    public function getMinDeep(ilObjSrContainerObjectTree $object) : int
    {
        if ($this->min_deep[$object->getId()] === null) {
            $this->min_deep[$object->getId()] = 1;
        }

        return $this->min_deep[$object->getId()];
    }


    /**
     * @param ilObjSrContainerObjectTree $object
     *
     * @return int
     */
    public function getStartDeep(ilObjSrContainerObjectTree $object) : int
    {
        if ($this->start_deep[$object->getId()] === null) {
            $this->start_deep[$object->getId()] = $object->getStartDeep();

            if ($this->start_deep[$object->getId()] === 0 || $this->start_deep[$object->getId()] < $this->getMinDeep($object)
                || $this->start_deep[$object->getId()] > $this->getMaxDeep($object)
            ) {
                $this->start_deep[$object->getId()] = $this->getDefaultDeep($object);
            }
        }

        return $this->start_deep[$object->getId()];
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {

    }


    /**
     * @param int                        $parent_ref_id
     * @param ilObjSrContainerObjectTree $object
     *
     * @return array[]
     */
    protected function getCountSubChildrenTypes(int $parent_ref_id, ilObjSrContainerObjectTree $object) : array
    {
        return array_values(array_map(function (array $count_sub_children_type) : array {
            $count_sub_children_type["type_title"] = self::srContainerObjectTree()->objects()->getObjectTypeTitle($count_sub_children_type["type"], ($count_sub_children_type["count"] !== 1));

            return $count_sub_children_type;
        }, array_reduce($this->getChildren($parent_ref_id, $object)["children"],
            function (array $count_sub_children_types, array $children) : array {
                $count_sub_children_types = $this->getCountSubChildrenTypesCount($count_sub_children_types, $children["type"]);

                foreach ($children["count_sub_children_types"] as $children2) {
                    $count_sub_children_types = $this->getCountSubChildrenTypesCount($count_sub_children_types, $children2["type"], $children2["count"]);
                }

                return $count_sub_children_types;
            }, [])));
    }


    /**
     * @param array[] $count_sub_children_types
     * @param string  $type
     * @param int     $additional_count
     *
     * @return array[]
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
