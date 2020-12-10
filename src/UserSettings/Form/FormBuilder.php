<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings\Form;

use ilObjSrContainerObjectTree;
use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrContainerObjectTree\UserSettings\Form
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
     * @var string[]
     */
    protected $start_deep_options = [];


    /**
     * @inheritDoc
     *
     * @param UserSettingsCtrl           $parent
     * @param ilObjSrContainerObjectTree $object
     */
    public function __construct(UserSettingsCtrl $parent, ilObjSrContainerObjectTree $object)
    {
        $this->object = $object;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        $html = parent::render();

        $html = str_replace('<select name="form_input_2"', '<select name="form_input_2" size="2"', $html);
        $html = str_replace('<select name="form_input_3"', '<select name="form_input_3" size="' . count($this->start_deep_options) . '"', $html);

        return $html;
    }


    /**
     * @inheritDoc
     */
    protected function getAction() : string
    {
        return self::dic()->ctrl()->getFormAction($this->parent, UserSettingsCtrl::CMD_UPDATE_USER_SETTINGS, "", true);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            UserSettingsCtrl::CMD_UPDATE_USER_SETTINGS => ""
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "show_metadata" => ($this->object->isShowMetadata() ? "show" : "hide"),
            "start_deep"    => self::srContainerObjectTree()->tree()->getStartDeep($this->object)
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $this->start_deep_options = array_reduce(range(self::srContainerObjectTree()->tree()->getMinDeep($this->object), self::srContainerObjectTree()->tree()->getMaxDeep($this->object)),
            function (array $start_deep_options, int $deep) : array {
                $start_deep_options[$deep] = self::plugin()->translate("deep_x", UserSettingsCtrl::LANG_MODULE, [$deep]);

                return $start_deep_options;
            }, []);

        $fields = [
            "show_metadata" => self::dic()->ui()->factory()->input()->field()->select("", [
                "show" => self::plugin()->translate("show_metadata", UserSettingsCtrl::LANG_MODULE),
                "hide" => self::plugin()->translate("hide_metadata", UserSettingsCtrl::LANG_MODULE)
            ])->withRequired(true),
            "start_deep"    => self::dic()->ui()->factory()->input()->field()->select("", $this->start_deep_options)->withRequired(true)
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return "";
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        $this->object->setShowMetadata($data["show_metadata"] === "show");
        $this->object->setMaxDeep(intval($data["start_deep"]));

        self::srContainerObjectTree()->userSettings()->storeUserSettings($this->object->getUserSettings());
    }
}
