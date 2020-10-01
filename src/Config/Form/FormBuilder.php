<?php

namespace srag\Plugins\SrContainerObjectTree\Config\Form;

use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrContainerObjectTree\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\CustomInputGUIs\SrContainerObjectTree\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\Plugins\SrContainerObjectTree\Config\ConfigCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrContainerObjectTree\Config\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectTreeTrait;

    const KEY_LINK_OBJECTS = "link_objects";
    const KEY_OBJECT_TYPES = "object_types";
    const KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY = "only_show_container_objects_if_not_empty";
    const KEY_RECURSIVE_COUNT = "recursive_count";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;


    /**
     * @inheritDoc
     *
     * @param ConfigCtrl $parent
     */
    public function __construct(ConfigCtrl $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ConfigCtrl::CMD_UPDATE_CONFIGURE => self::plugin()->translate("save", ConfigCtrl::LANG_MODULE)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            self::KEY_OBJECT_TYPES                             => self::srContainerObjectTree()->config()->getValue(self::KEY_OBJECT_TYPES),
            self::KEY_LINK_OBJECTS                             => self::srContainerObjectTree()->config()->getValue(self::KEY_LINK_OBJECTS),
            self::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY => self::srContainerObjectTree()->config()->getValue(self::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY),
            self::KEY_RECURSIVE_COUNT                          => self::srContainerObjectTree()->config()->getValue(self::KEY_RECURSIVE_COUNT)
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            self::KEY_OBJECT_TYPES                             => (new InputGUIWrapperUIInputComponent(new MultiSelectSearchNewInputGUI(self::plugin()
                ->translate(self::KEY_OBJECT_TYPES, ConfigCtrl::LANG_MODULE))))->withRequired(true),
            self::KEY_LINK_OBJECTS                             => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()
                ->translate(self::KEY_LINK_OBJECTS, ConfigCtrl::LANG_MODULE)),
            self::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()
                ->translate(self::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY, ConfigCtrl::LANG_MODULE)),
            self::KEY_RECURSIVE_COUNT                          => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()
                ->translate(self::KEY_RECURSIVE_COUNT, ConfigCtrl::LANG_MODULE))
        ];

        $fields[self::KEY_OBJECT_TYPES]->getInput()->setOptions(self::srContainerObjectTree()->tree()->getObjectTypes(null, false));

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("configuration", ConfigCtrl::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        self::srContainerObjectTree()->config()->setValue(self::KEY_OBJECT_TYPES, MultiSelectSearchNewInputGUI::cleanValues((array) $data[self::KEY_OBJECT_TYPES]));
        self::srContainerObjectTree()->config()->setValue(self::KEY_LINK_OBJECTS, boolval($data[self::KEY_LINK_OBJECTS]));
        self::srContainerObjectTree()->config()->setValue(self::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY, boolval($data[self::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY]));
        self::srContainerObjectTree()->config()->setValue(self::KEY_RECURSIVE_COUNT, boolval($data[self::KEY_RECURSIVE_COUNT]));
    }
}
