<?php

namespace srag\Plugins\SrContainerObjectTree\UserSettings\Form;

use ilSrContainerObjectTreePlugin;
use srag\CustomInputGUIs\SrContainerObjectTree\FormBuilder\AbstractFormBuilder;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettings;
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
     * @var int
     */
    protected $tree_end_deep;
    /**
     * @var int
     */
    protected $tree_start_deep;
    /**
     * @var UserSettings
     */
    protected $user_settings;


    /**
     * @inheritDoc
     *
     * @param UserSettingsCtrl $parent
     * @param UserSettings     $user_settings
     * @param int              $tree_start_deep
     * @param int              $tree_end_deep
     */
    public function __construct(UserSettingsCtrl $parent, UserSettings $user_settings, int $tree_start_deep, int $tree_end_deep)
    {
        $this->user_settings = $user_settings;
        $this->tree_start_deep = $tree_start_deep;
        $this->tree_end_deep = $tree_end_deep;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        if (self::version()->is6()) {
            $glyph_factory = self::dic()->ui()->factory()->symbol()->glyph();
        } else {
            $glyph_factory = self::dic()->ui()->factory()->glyph();
        }

        return self::output()->getHTML([$glyph_factory->settings(), parent::render()]);
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
            "max_deep" => $this->user_settings->getMaxDeep(
                $this->tree_end_deep
            )
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
                array_reduce(range($this->tree_start_deep, $this->tree_end_deep), function (array $max_deep, int $deep) : array {
                    $max_deep[$deep] = self::plugin()->translate("deep_x", UserSettingsCtrl::LANG_MODULE, [$deep]);

                    return $max_deep;
                }, []))->withRequired(true)
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

        self::srContainerObjectTree()->userSettings()->storeUserSettings($this->user_settings);
    }
}
