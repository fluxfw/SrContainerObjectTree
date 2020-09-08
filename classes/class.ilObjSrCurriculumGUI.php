<?php

use srag\DIC\SrCurriculum\DICTrait;
use srag\Plugins\SrCurriculum\ObjectSettings\Form\FormBuilder;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;

/**
 * Class ilObjSrCurriculumGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrCurriculumGUI: ilRepositoryGUI
 * @ilCtrl_isCalledBy ilObjSrCurriculumGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjSrCurriculumGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjSrCurriculumGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjSrCurriculumGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjSrCurriculumGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjSrCurriculumGUI: ilCommonActionDispatcherGUI
 */
class ilObjSrCurriculumGUI extends ilObjectPluginGUI
{

    use DICTrait;
    use SrCurriculumTrait;

    const CMD_MANAGE_CONTENTS = "manageContents";
    const CMD_PERMISSIONS = "perm";
    const CMD_SETTINGS = "settings";
    const CMD_SETTINGS_STORE = "settingsStore";
    const CMD_SHOW_CONTENTS = "showContents";
    const LANG_MODULE_OBJECT = "object";
    const LANG_MODULE_SETTINGS = "settings";
    const PLUGIN_CLASS_NAME = ilSrCurriculumPlugin::class;
    const TAB_CONTENTS = "contents";
    const TAB_PERMISSIONS = "perm_settings";
    const TAB_SETTINGS = "settings";
    const TAB_SHOW_CONTENTS = "show_contents";
    /**
     * @var ilObjSrCurriculum
     */
    public $object;


    /**
     * @return string
     */
    public static function getStartCmd() : string
    {
        if (ilObjSrCurriculumAccess::hasWriteAccess()) {
            return self::CMD_MANAGE_CONTENTS;
        } else {
            return self::CMD_SHOW_CONTENTS;
        }
    }


    /**
     * @inheritDoc
     *
     * @param ilObjSrCurriculum $a_new_object
     */
    public function afterSave(/*ilObjSrCurriculum*/ ilObject $a_new_object)/* : void*/
    {
        parent::afterSave($a_new_object);
    }


    /**
     * @inheritDoc
     */
    public function getAfterCreationCmd() : string
    {
        return self::getStartCmd();
    }


    /**
     * @inheritDoc
     */
    public function getStandardCmd() : string
    {
        return self::getStartCmd();
    }


    /**
     * @inheritDoc
     */
    public final function getType() : string
    {
        return ilSrCurriculumPlugin::PLUGIN_ID;
    }


    /**
     * @inheritDoc
     */
    public function initCreateForm(/*string*/ $a_new_type) : ilPropertyFormGUI
    {
        $form = parent::initCreateForm($a_new_type);

        return $form;
    }


    /**
     * @param string $cmd
     */
    public function performCommand(string $cmd)/* : void*/
    {
        self::dic()->help()->setScreenIdComponent(ilSrCurriculumPlugin::PLUGIN_ID);

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                switch ($cmd) {
                    case self::CMD_SHOW_CONTENTS:
                        // Read commands
                        if (!ilObjSrCurriculumAccess::hasReadAccess()) {
                            ilObjSrCurriculumAccess::redirectNonAccess(ilRepositoryGUI::class);
                        }

                        $this->{$cmd}();
                        break;

                    case self::CMD_MANAGE_CONTENTS:
                    case self::CMD_SETTINGS:
                    case self::CMD_SETTINGS_STORE:
                        // Write commands
                        if (!ilObjSrCurriculumAccess::hasWriteAccess()) {
                            ilObjSrCurriculumAccess::redirectNonAccess($this);
                        }

                        $this->{$cmd}();
                        break;

                    default:
                        // Unknown command
                        ilObjSrCurriculumAccess::redirectNonAccess(ilRepositoryGUI::class);
                        break;
                }
                break;
        }
    }


    /**
     * @inheritDoc
     */
    protected function afterConstructor()/* : void*/
    {

    }


    /**
     * @return FormBuilder
     */
    protected function getSettingsForm() : FormBuilder
    {
        $form = new FormBuilder($this, $this->object);

        return $form;
    }


    /**
     *
     */
    protected function manageContents()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        // TODO: Implement manageContents
        $this->show("");
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {
        self::dic()->tabs()->addTab(self::TAB_SHOW_CONTENTS, self::plugin()->translate("show_contents", self::LANG_MODULE_OBJECT), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

        if (ilObjSrCurriculumAccess::hasWriteAccess()) {
            self::dic()->tabs()->addTab(self::TAB_CONTENTS, self::plugin()->translate("manage_contents", self::LANG_MODULE_OBJECT), self::dic()
                ->ctrl()->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

            self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings", self::LANG_MODULE_SETTINGS), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_SETTINGS));
        }

        if (ilObjSrCurriculumAccess::hasEditPermissionAccess()) {
            self::dic()->tabs()->addTab(self::TAB_PERMISSIONS, self::plugin()->translate(self::TAB_PERMISSIONS, "", [], false), self::dic()->ctrl()
                ->getLinkTargetByClass([
                    self::class,
                    ilPermissionGUI::class
                ], self::CMD_PERMISSIONS));
        }

        self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
    }


    /**
     *
     */
    protected function settings()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = $this->getSettingsForm();

        self::output()->output($form);
    }


    /**
     *
     */
    protected function settingsStore()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = $this->getSettingsForm();

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE_SETTINGS), true);

        self::dic()->ctrl()->redirect($this, self::CMD_SETTINGS);
    }


    /**
     * @param string $html
     */
    protected function show(string $html)/* : void*/
    {
        if (!self::dic()->ctrl()->isAsynch()) {
            self::dic()->ui()->mainTemplate()->setTitle($this->object->getTitle());

            self::dic()->ui()->mainTemplate()->setDescription($this->object->getDescription());

            if (!$this->object->isOnline()) {
                self::dic()->ui()->mainTemplate()->setAlertProperties([
                    [
                        "alert"    => true,
                        "property" => self::plugin()->translate("status", self::LANG_MODULE_OBJECT),
                        "value"    => self::plugin()->translate("offline", self::LANG_MODULE_OBJECT)
                    ]
                ]);
            }
        }

        self::output()->output($html);
    }


    /**
     *
     */
    protected function showContents()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SHOW_CONTENTS);

        // TODO: Implement showContents
        $this->show("");
    }
}
