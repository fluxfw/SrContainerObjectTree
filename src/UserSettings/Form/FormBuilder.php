<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings\Form;

use ilObjSrContainerObjectTree;
use ilSrContainerObjectTreePlugin;
use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrContainerObjectTree\UserSettings\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var ilObjSrContainerObjectTree
     */
    protected $object;
    /**
     * @var UserSettingsCtrl
     */
    protected $parent;


    /**
     * @inheritDoc
     *
     * @param UserSettingsCtrl           $parent
     * @param ilObjSrContainerObjectTree $object
     */
    public function __construct(UserSettingsCtrl $parent, ilObjSrContainerObjectTree $object)
    {
        $this->parent = $parent;
        $this->object = $object;
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $tpl = self::plugin()->template("SrContainerObjectUserSettingsForm.html");

        $tpl->setVariableEscaped("FORM_ACTION", self::dic()->ctrl()->getFormAction($this->parent, UserSettingsCtrl::CMD_UPDATE_USER_SETTINGS, "", true));

        $tpl->setCurrentBlock("field");
        foreach (
            [
                $this->getSelectHtml("show_metadata", [
                    "show" => self::plugin()->translate("show_metadata", UserSettingsCtrl::LANG_MODULE),
                    "hide" => self::plugin()->translate("hide_metadata", UserSettingsCtrl::LANG_MODULE)
                ], ($this->object->isShowMetadata() ? "show" : "hide")),
                $this->getSelectHtml("start_deep",
                    array_reduce(range(self::srContainerObjectTree()->tree()->getMinDeep($this->object), self::srContainerObjectTree()->tree()->getMaxDeep($this->object)),
                        function (array $start_deep_options, int $deep) : array {
                            $start_deep_options[$deep] = self::plugin()->translate("deep_x", UserSettingsCtrl::LANG_MODULE, [$deep]);

                            return $start_deep_options;
                        }, []), self::srContainerObjectTree()->tree()->getStartDeep($this->object))
            ] as $field
        ) {
            $tpl->setVariable("FIELD", $field);
            $tpl->parseCurrentBlock();
        }

        return self::output()->getHTML($tpl);
    }


    /**
     *
     */
    public function storeData()/* : void*/
    {
        $this->object->setShowMetadata(filter_input(INPUT_POST, "show_metadata") === "show");
        $this->object->setStartDeep(intval(filter_input(INPUT_POST, "start_deep")));

        self::srContainerObjectTree()->userSettings()->storeUserSettings($this->object->getUserSettings());
    }


    /**
     * @param string $name
     * @param array  $options
     * @param string $selected_value
     *
     * @return string
     */
    protected function getSelectHtml(string $name, array $options, string $selected_value) : string
    {
        $tpl = self::plugin()->template("SrContainerObjectUserSettingsFormSelect.html");

        $tpl->setVariableEscaped("NAME", $name);
        $tpl->setVariableEscaped("SIZE", count($options));

        $tpl->setCurrentBlock("option");
        foreach ($options as $value => $txt) {
            if (strval($value) === $selected_value) {
                $tpl->setVariableEscaped("SELECTED", "selected");
            }
            $tpl->setVariableEscaped("VALUE", $value);
            $tpl->setVariableEscaped("TXT", $txt);
            $tpl->parseCurrentBlock();
        }

        return self::output()->getHTML($tpl);
    }
}
