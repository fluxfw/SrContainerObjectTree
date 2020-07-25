<?php

namespace srag\Plugins\SrCurriculum\Config\Form;

use srag\Plugins\SrCurriculum\Config\ConfigCtrl;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;
use ilSrCurriculumPlugin;
use srag\CustomInputGUIs\SrCurriculum\FormBuilder\AbstractFormBuilder;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrCurriculum\Config\Form
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrCurriculumTrait;

    const KEY_SOME = "some";
    const PLUGIN_CLASS_NAME = ilSrCurriculumPlugin::class;


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
            self::KEY_SOME => self::srCurriculum()->config()->getValue(self::KEY_SOME)
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            self::KEY_SOME => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate(self::KEY_SOME, ConfigCtrl::LANG_MODULE))->withRequired(true)
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
        self::srCurriculum()->config()->setValue(self::KEY_SOME, strval($data[self::KEY_SOME]));
    }
}
