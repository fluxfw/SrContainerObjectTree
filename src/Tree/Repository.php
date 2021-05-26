<?php

namespace srag\Plugins\SrContainerObjectTree\Tree;

use ilContainer;
use ilLink;
use ilObject;
use ilObjSrContainerObjectTree;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\DIC\SrContainerObjectTree\Version\PluginVersionParameter;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrContainerObjectTree\Tree
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
    protected $parent_deep = [];
    /**
     * @var int[]
     */
    protected $parent_ref_id = [];
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
     * @param ilObjSrContainerObjectTree $object
     *
     * @return array
     */
    public function fetchTree(ilObjSrContainerObjectTree $object) : array
    {
        return [
            "tree_children"   => $this->getChildren($this->getParentRefId($object), $object),
            "tree_max_deep"   => $this->getMaxDeep($object),
            "tree_min_deep"   => $this->getMinDeep($object),
            "tree_start_deep" => $this->getStartDeep($object)
        ];
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

            $parent_object = self::srContainerObjectTree()->objects()->getObjectByRefId($parent_ref_id);

            if ($parent_object === null
                || !in_array($parent_object->getType(), self::srContainerObjectTree()->objects()->getContainerObjectTypes(self::srContainerObjectTree()->config()->getObjectTypes()))
                || !($parent_object instanceof ilContainer)
                || !self::srContainerObjectTree()->objects()->hasReadAccess($parent_ref_id)
            ) {
                return $children;
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

                $child = [
                    "children"                 => $this->getChildren($ref_id, $object),
                    "count_sub_children_types" => $count_sub_children_types_count,
                    "description"              => $sub_item["description"],
                    "icon"                     => ilObject::_getIcon($sub_item["obj_id"]),
                    "is_container"             => $is_container,
                    "link"                     => ilLink::_getLink($ref_id),
                    "ref_id"                   => $ref_id,
                    "title"                    => $sub_item["title"],
                    "type"                     => $type
                ];

                self::dic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilSrContainerObjectTreePlugin::PLUGIN_NAME, ilSrContainerObjectTreePlugin::EVENT_CHANGE_CHILD_BEFORE_OUTPUT, [
                    "child" => &$child // Unfortunately ILIAS Raise Event System not supports return results so use a referenced variable
                ]);

                $children[] = $child;
            }

            $this->children[$cache_key] = $children;
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
            $this->default_deep[$object->getId()] = 1;
        }

        return $this->default_deep[$object->getId()];
    }


    /**
     * @param ilObjSrContainerObjectTree $object
     * @param string                     $tree_fetch_url
     * @param string                     $edit_user_settings_update_url
     *
     * @return string
     */
    public function getHtml(ilObjSrContainerObjectTree $object, string $tree_fetch_url, string $edit_user_settings_update_url) : string
    {
        if (self::version()->is6()) {
            $glyph_factory = self::dic()->ui()->factory()->symbol()->glyph();
        } else {
            $glyph_factory = self::dic()->ui()->factory()->glyph();
        }

        $version_parameter = PluginVersionParameter::getInstance()->withPlugin(self::plugin());

        self::dic()->ui()->mainTemplate()->addCss($version_parameter->appendToUrl(self::plugin()->directory() . "/css/SrContainerObjectTree.css"));
        self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl(self::plugin()->directory() . "/js/SrContainerObjectTree.min.js",
            self::plugin()->directory() . "/js/SrContainerObjectTree.js"));

        $tpl = self::plugin()->template("SrContainerObjectTree.html");
        $config = [
            "edit_user_settings_update_url" => $edit_user_settings_update_url,
            "obj_ref_id"                    => $object->getRefId(),
            "plugin_version"                => self::plugin()->getPluginObject()->getVersion(),
            "texts"                         => [
                "edit_user_settings_deep_x"        => self::plugin()->translate("deep_x", UserSettingsCtrl::LANG_MODULE),
                "edit_user_settings_hide_metadata" => self::plugin()->translate("hide_metadata", UserSettingsCtrl::LANG_MODULE),
                "edit_user_settings_save_error"    => self::plugin()->translate("save_error", UserSettingsCtrl::LANG_MODULE),
                "edit_user_settings_show_metadata" => self::plugin()->translate("show_metadata", UserSettingsCtrl::LANG_MODULE),
                "tree_apply"                       => self::plugin()->translate("apply", TreeCtrl::LANG_MODULE),
                "tree_empty"                       => self::plugin()->translate("empty", TreeCtrl::LANG_MODULE),
                "tree_fetch_error"                 => self::plugin()->translate("fetch_error", TreeCtrl::LANG_MODULE),
                "tree_has_changed_meanwhile"       => self::plugin()->translate("has_changed_meanwhile", TreeCtrl::LANG_MODULE),
                "tree_loaded_from_cache"           => self::plugin()->translate("loaded_from_cache", TreeCtrl::LANG_MODULE)
            ],
            "tree_fetch_url"                => $tree_fetch_url,
            "tree_link_container_objects"   => self::srContainerObjectTree()->config()->isLinkContainerObjects(),
            "tree_link_new_tab"             => self::srContainerObjectTree()->config()->isOpenLinksInNewTab(),
            "tree_show_metadata"            => $object->isShowMetadata()
        ];
        $tpl->setVariableEscaped("CONFIG", base64_encode(json_encode($config)));

        $tpl_tree = self::plugin()->template("SrContainerObjectTreeTree.html", true, false);

        $tpl_user_settings = self::plugin()->template("SrContainerObjectTreeEditUserSettings.html");

        $tpl_user_settings_icon = self::plugin()->template("SrContainerObjectTreeEditUserSettingsIcon.html");
        $tpl_user_settings_icon->setVariable("USER_SETTINGS_ICON", self::output()->getHTML($glyph_factory->settings()));
        $tpl_user_settings->setVariable("USER_SETTINGS_ICON", self::output()->getHTML($tpl_user_settings_icon));

        $tpl_user_settings_form_container = self::plugin()->template("SrContainerObjectUserSettingsFormContainer.html", true, false);
        $tpl_user_settings->setVariable("USER_SETTINGS_FORM_CONTAINER", self::output()->getHTML($tpl_user_settings_form_container));

        $tpl->setVariable("TREE", self::output()->getHTML($tpl_tree));
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
            $children = self::srContainerObjectTree()->objects()->getSubTree($this->getParentRefId($object));

            $ref_id_deep = $max_deep = $children[$this->getParentRefId($object)]["depth"];

            foreach ($children as $child) {
                if ($child["depth"] <= $max_deep) {
                    continue;
                }

                if (!empty($this->getChildren($child["child"], $object))) {
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
    public function getParentDeep(ilObjSrContainerObjectTree $object) : int
    {
        if ($this->parent_deep[$object->getId()] === null) {
            $parent_ref_id = $object->getContainerRefId();

            if (!empty($parent_ref_id)) {
                $children = self::srContainerObjectTree()->objects()->getSubTree($parent_ref_id);

                $ref_id_deep = $parent_deep = $children[$parent_ref_id]["depth"];

                foreach ($children as $child) {
                    if (count($this->getChildren($child["child"], $object)) > 1) {
                        $parent_deep = $child["depth"];
                        $parent_ref_id = $child["child"];
                        break;
                    }
                }

                $this->parent_deep[$object->getId()] = ($parent_deep - $ref_id_deep + 1);
            } else {
                $this->parent_deep[$object->getId()] = 1;
            }

            $this->parent_ref_id[$object->getId()] = $parent_ref_id;
        }

        return $this->parent_deep[$object->getId()];
    }


    /**
     * @param ilObjSrContainerObjectTree $object
     *
     * @return int
     */
    public function getParentRefId(ilObjSrContainerObjectTree $object) : int
    {
        if ($this->parent_ref_id[$object->getId()] === null) {
            $this->getParentDeep($object);
        }

        return $this->parent_ref_id[$object->getId()];
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
        }, array_reduce($this->getChildren($parent_ref_id, $object),
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
