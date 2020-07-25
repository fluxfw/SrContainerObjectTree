<?php

namespace srag\Plugins\SrCurriculum\Curriculum\Application;

use srag\DIC\SrCurriculum\DICTrait;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;
use ilLink;
use srag\Plugins\SrUserEnrolment\RuleEnrolment\Logs\Factory;
use ilAccess;
use ilPermission2GUI;

/**
 * Class CurriculumApplicationService
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author            Martin Studer <ms@studer-raimann.ch>
 */
class CurriculumApplicationService
{
    use DICTrait;
    use SrCurriculumTrait;
    /** @var string  */
    protected $il_gui_class_name;
    /** @var string  */
    protected $il_gui_command;
    /** @var int  */
    protected $start_node_ref_id = 0;


    public const CMD_VAR_NAME = "curriculumcmd";
    public const CMD_GET_TREE = "getTree";

    private function __construct() {

    }

    public static function new(
        string $il_gui_class_name,
        string $il_gui_command,
        int $start_node_ref_id
    )
    {
        $obj = new self();
        $obj->start_node_ref_id = $start_node_ref_id;
        $obj->il_gui_class_name = $il_gui_class_name;
        $obj->il_gui_command = $il_gui_command;
        return $obj;
    }

    public function run()
    {
        $curriculum_cmd = filter_input(INPUT_GET, self::CMD_VAR_NAME, FILTER_SANITIZE_STRING);
        switch($curriculum_cmd) {
            case self::CMD_GET_TREE:
                $this->getTree();
                break;
            default:
                return $this->getHtml();
                break;
        }
    }

    public function getHtml() {
        $tpl = self::dic()->templateFactory()->getTemplate(__DIR__."/../UI/build/index.html",
            false, false);

        self::dic()->ctrl()->setParameterByClass(
            $this->il_gui_class_name,
            self::CMD_VAR_NAME,
            self::CMD_GET_TREE
        );
        $tpl->setVariable(
            'TREESERVICE',
            self::dic()->ctrl()->getLinkTargetByClass(
                $this->il_gui_class_name,
                $this->il_gui_command,
                "",
                false,
                false
            )
        );

        $tpl->setVariable(
            'NODEID',
            $this->start_node_ref_id
        );

        $tpl->setVariable(
            'NODEDEPTH',
            self::dic()->repositoryTree()->getDepth($this->start_node_ref_id)
        );



        return $tpl->get();
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand()
    {
        $show_nav = filter_input(INPUT_GET, 'show_nav', FILTER_SANITIZE_NUMBER_INT);
        self::dic()->ctrl()->saveParameter(
            $this, 'show_nav'
        );

        $cmd = self::dic()->ctrl()->getCmd();

        switch ($cmd) {
            case 'getTree':
                $this->getTree();
                break;
            default:
                $this->getHtml();
                break;
        }
    }


    private function getTree()
    {
        $start_node_id = json_decode(filter_input(INPUT_GET, 'node_id', FILTER_SANITIZE_NUMBER_INT));
        $start_depth = json_decode(filter_input(INPUT_GET, 'node_depth', FILTER_SANITIZE_NUMBER_INT)
        );


       $childs = self::dic()->repositoryTree()->getFilteredSubTree($start_node_id);

       $max_depth = $start_depth + 2;

        $tree = [];
        $start_node_depth = $childs[0]['depth'];
        //transformation depth to 1.
        $transformation_difference = $start_node_depth - $start_depth;
        foreach ($childs as $child) {

            if($child['type'] != "cat"
                && $child['type'] != "crs"
                && $child['type'] != "grp"
            ) {
                continue;
            }
            $transformed_depth = $child['depth'] - $transformation_difference;

            if(!self::dic()->access()->checkAccess('read','',$child['child'],$child['type'])) {
                continue;
            }

            switch($child['type']) {
                case 'cat':
                    if($transformed_depth >= $max_depth) {
                        continue 2;
                    }
                    $parentId = null;
                    if ((int) $start_node_id != (int) $child['child'] || $transformed_depth != 1) {
                        $parentId = $child['parent'];
                    }


                    $tree[$child['child']] = [
                        'label' => $child['title'],
                        'id' =>$child['child'],
                        'parentId' => $parentId,
                        'items' => [],
                        'depth' => $transformed_depth,
                        'type' => 'cat'
                    ];
                    break;
                case 'crs':
                case 'grp':
                if($transformed_depth > $max_depth) {
                    continue 2;
                }
                    $tree[$child['parent']]['items'][] = [
                        'label' => $child['title'],
                        'id' =>$child['child'],
                        'parentId' => $child['parent'],
                        'depth' => $transformed_depth,
                        'type' => $child['type'],
                        'link' => ilLink::_getLink(
                            $child['child'],
                            $child['type']
                        )
                    ];
                    break;
            }
        }

        $rearranged_indexes = [];
        foreach($tree as $node) {
            $rearranged_indexes[] = $node;
        }
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($rearranged_indexes);
        exit();
    }
}