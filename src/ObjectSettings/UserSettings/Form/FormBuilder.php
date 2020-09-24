<?php

namespace srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\Form;

use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\UserSettings;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrContainerObjectTree\ObjectSettings\UserSettings\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var UserSettings
     */
    protected $user_settings;


    /**
     * @inheritDoc
     *
     * @param UserSettingsCtrl $parent
     * @param UserSettings     $user_settings
     */
    public function __construct(UserSettingsCtrl $parent, UserSettings $user_settings)
    {
        $this->user_settings = $user_settings;

        parent::__construct($parent);
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
            "max_deep" => $this->user_settings->getMaxDeep()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "max_deep" => self::dic()->ui()->factory()->input()->field()->select("",
                array_map(function (int $deep) : string {
                    return self::plugin()->translate("deep_" . $deep, UserSettingsCtrl::LANG_MODULE);
                }, range(0, 2)))->withRequired(true)
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
        $this->user_settings->setMaxDeep(intval($data["max_deep"]));

        self::srContainerObjectTree()->objectSettings()->userSettings()->storeUserSettings($this->user_settings);
    }
}
