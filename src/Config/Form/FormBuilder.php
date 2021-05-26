<?php

namespace srag\Plugins\SrContainerObjectTree\Config\Form;

use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrContainerObjectTree\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\CustomInputGUIs\SrContainerObjectTree\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\Plugins\SrContainerObjectTree\Config\ConfigCtrl;
use srag\Plugins\SrContainerObjectTree\Config\Repository;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrContainerObjectTree\Config\Form
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectTreeTrait;

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
            Repository::KEY_OBJECT_TYPES                         => self::srContainerObjectTree()->config()->getObjectTypes(),
            Repository::KEY_LINK_CONTAINER_OBJECTS               => self::srContainerObjectTree()->config()->isLinkContainerObjects(),
            Repository::KEY_OPEN_LINKS_IN_NEW_TAB                => self::srContainerObjectTree()->config()->isOpenLinksInNewTab(),
            Repository::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES => self::srContainerObjectTree()->config()->getAllowedEmptyContainerObjectTypes()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            Repository::KEY_OBJECT_TYPES                         => (new InputGUIWrapperUIInputComponent(new MultiSelectSearchNewInputGUI(self::plugin()
                ->translate(Repository::KEY_OBJECT_TYPES, ConfigCtrl::LANG_MODULE))))->withRequired(true),
            Repository::KEY_LINK_CONTAINER_OBJECTS               => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()
                ->translate(Repository::KEY_LINK_CONTAINER_OBJECTS, ConfigCtrl::LANG_MODULE)),
            Repository::KEY_OPEN_LINKS_IN_NEW_TAB                => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()
                ->translate(Repository::KEY_OPEN_LINKS_IN_NEW_TAB, ConfigCtrl::LANG_MODULE)),
            Repository::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES => (new InputGUIWrapperUIInputComponent(new MultiSelectSearchNewInputGUI(self::plugin()
                ->translate(Repository::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES, ConfigCtrl::LANG_MODULE))))
        ];

        $fields[Repository::KEY_OBJECT_TYPES]->getInput()->setOptions(self::srContainerObjectTree()->objects()->getObjectTypes(null, false));
        $fields[Repository::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES]->getInput()->setOptions(self::srContainerObjectTree()->objects()->getContainerObjectTypes(null, false));

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
        self::srContainerObjectTree()->config()->setObjectTypes(MultiSelectSearchNewInputGUI::cleanValues((array) $data[Repository::KEY_OBJECT_TYPES]));
        self::srContainerObjectTree()->config()->setLinkContainerObjects(boolval($data[Repository::KEY_LINK_CONTAINER_OBJECTS]));
        self::srContainerObjectTree()->config()->setOpenLinksInNewTab(boolval($data[Repository::KEY_OPEN_LINKS_IN_NEW_TAB]));
        self::srContainerObjectTree()->config()->setAllowedEmptyContainerObjectTypes(MultiSelectSearchNewInputGUI::cleanValues((array) $data[Repository::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES]));
    }
}
