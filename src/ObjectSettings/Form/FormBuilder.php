<?php

namespace srag\Plugins\SrContainerObjectTree\ObjectSettings\Form;

use ilObjSrContainerObjectTree;
use ilObjSrContainerObjectTreeGUI;
use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrContainerObjectTree\ObjectSettings\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var ilObjSrContainerObjectTree
     */
    protected $object;


    /**
     * @inheritDoc
     *
     * @param ilObjSrContainerObjectTreeGUI $parent
     * @param ilObjSrContainerObjectTree    $object
     */
    public function __construct(ilObjSrContainerObjectTreeGUI $parent, ilObjSrContainerObjectTree $object)
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
            ilObjSrContainerObjectTreeGUI::CMD_SETTINGS_STORE => self::plugin()->translate("save", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS),
            ilObjSrContainerObjectTreeGUI::CMD_SHOW_CONTENTS  => self::plugin()->translate("cancel", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS)
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
            "title"       => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate("title", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS))->withRequired(true),
            "description" => self::dic()->ui()->factory()->input()->field()->textarea(self::plugin()->translate("description", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS)),
            "online"      => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate("online", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS))
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("settings", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS);
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
