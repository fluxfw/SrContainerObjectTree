<?php

namespace srag\Plugins\SrCurriculum\ObjectSettings\Form;

use ilObjSrCurriculum;
use ilObjSrCurriculumGUI;
use ilSrCurriculumPlugin;
use srag\CustomInputGUIs\SrCurriculum\FormBuilder\AbstractFormBuilder;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrCurriculum\ObjectSettings\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrCurriculumTrait;

    const PLUGIN_CLASS_NAME = ilSrCurriculumPlugin::class;
    /**
     * @var ilObjSrCurriculum
     */
    protected $object;


    /**
     * @inheritDoc
     *
     * @param ilObjSrCurriculumGUI $parent
     * @param ilObjSrCurriculum    $object
     */
    public function __construct(ilObjSrCurriculumGUI $parent, ilObjSrCurriculum $object)
    {
        $this->object = $object;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ilObjSrCurriculumGUI::CMD_SETTINGS_STORE => self::plugin()->translate("save", ilObjSrCurriculumGUI::LANG_MODULE_SETTINGS),
            ilObjSrCurriculumGUI::CMD_SHOW_CONTENTS  => self::plugin()->translate("cancel", ilObjSrCurriculumGUI::LANG_MODULE_SETTINGS)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "title"       => $this->object->getTitle(),
            "description" => $this->object->getLongDescription(),
            "online"      => $this->object->isOnline()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "title"       => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate("title", ilObjSrCurriculumGUI::LANG_MODULE_SETTINGS))->withRequired(true),
            "description" => self::dic()->ui()->factory()->input()->field()->textarea(self::plugin()->translate("description", ilObjSrCurriculumGUI::LANG_MODULE_SETTINGS)),
            "online"      => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate("online", ilObjSrCurriculumGUI::LANG_MODULE_SETTINGS))
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("settings", ilObjSrCurriculumGUI::LANG_MODULE_SETTINGS);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        $this->object->setTitle(strval($data["title"]));
        $this->object->setDescription(strval($data["description"]));
        $this->object->setOnline(boolval($data["online"]));

        $this->object->update();
    }
}
