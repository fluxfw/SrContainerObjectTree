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
     * @param int $ref_id
     *
     * @return array
     */
    public function getChildren(int $ref_id) : array
    {
        $children = [];

        $object = ilObjectFactory::getInstanceByRefId($ref_id, false);

        if (!($object instanceof ilContainer)) {
            return $children;
        }

        if (!self::dic()->access()->checkAccess("read", "", $ref_id)) {
            return $children;
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

                $children[] = [
                    "icon"         => ilObject::_getIcon($sub_item["obj_id"]),
                    "is_container" => in_array($sub_item["type"], self::CONTAINER_TYPES),
                    "link"         => ilLink::_getLink($sub_item["child"]),
                    "ref_id"       => $sub_item["child"],
                    "title"        => $sub_item["title"]
                ];
            }
        }

        return $children;
    }


    /**
     * @param int    $container_ref_id
     * @param string $fetch_url
     *
     * @return string
     */
    public function getHtml(int $container_ref_id, string $fetch_url) : string
    {
        self::dic()->ui()->mainTemplate()->addCss(substr(self::plugin()->directory(), 2) . "/css/SrContainerObjectTree.css");
        self::dic()->ui()->mainTemplate()->addJavaScript(substr(self::plugin()->directory(), 2) . "/js/SrContainerObjectTree.min.js");

        $tpl = self::plugin()->template("SrContainerObjectTree.html");

        $config = [
            "container_ref_id" => $container_ref_id,
            "empty_text"       => self::plugin()->translate("empty", TreeCtrl::LANG_MODULE),
            "error_text"       => self::plugin()->translate("error", TreeCtrl::LANG_MODULE),
            "fetch_url"        => $fetch_url
                . "="
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
}
