<?php

namespace srag\Plugins\SrContainerObjectTree\ObjectSettings\Form;

use ilObjSrContainerObjectTree;
use ilObjSrContainerObjectTreeGUI;
use ilRepositorySelector2InputGUI;
use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrContainerObjectTree\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\Plugins\SrContainerObjectTree\Tree\Repository;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class FormBuilder
 *
 * @package      srag\Plugins\SrContainerObjectTree\ObjectSettings\Form
 *
 * @author       studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_Calls srag\Plugins\SrContainerObjectTree\ObjectSettings\Form\FormBuilder: ilFormPropertyDispatchGUI
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
            "title"            => $this->object->getTitle(),
            "description"      => $this->object->getLongDescription(),
            "online"           => $this->object->isOnline(),
            "container_ref_id" => $this->object->getContainerRefId()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "title"            => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate("title", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS))->withRequired(true),
            "description"      => self::dic()->ui()->factory()->input()->field()->textarea(self::plugin()->translate("description", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS)),
            "online"           => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate("online", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS)),
            "container_ref_id" => (new InputGUIWrapperUIInputComponent(new ilRepositorySelector2InputGUI(self::plugin()
                ->translate("container_object", ilObjSrContainerObjectTreeGUI::LANG_MODULE_SETTINGS),
                "container_ref_id", null, self::class)))->withRequired(true)
        ];
        $fields["container_ref_id"]->getInput()->getExplorerGUI()->setSelectableTypes(Repository::CONTAINER_TYPES);

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
        $this->object->setContainerRefId(intval($data["container_ref_id"]));

        $this->object->update();
    }
}
