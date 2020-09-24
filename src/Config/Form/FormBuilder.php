<?php

namespace srag\Plugins\SrContainerObjectTree\Config\Form;

use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
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
            self::KEY_LINK_OBJECTS => self::srContainerObjectTree()->config()->getValue(self::KEY_LINK_OBJECTS)
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            self::KEY_LINK_OBJECTS => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate(self::KEY_LINK_OBJECTS, ConfigCtrl::LANG_MODULE))
        ];

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
        self::srContainerObjectTree()->config()->setValue(self::KEY_LINK_OBJECTS, boolval($data[self::KEY_LINK_OBJECTS]));
    }
}
