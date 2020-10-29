<?php

use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Config\Form\FormBuilder as ConfigFormBuilder;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\Form\FormBuilder;
use srag\Plugins\SrContainerObjectTree\Tree\TreeCtrl;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettings;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettingsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class ilObjSrContainerObjectTreeGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrContainerObjectTreeGUI: ilRepositoryGUI
 * @ilCtrl_isCalledBy ilObjSrContainerObjectTreeGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjSrContainerObjectTreeGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjSrContainerObjectTreeGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjSrContainerObjectTreeGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjSrContainerObjectTreeGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjSrContainerObjectTreeGUI: ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjSrContainerObjectTreeGUI: srag\Plugins\SrContainerObjectTree\ObjectSettings\Form\FormBuilder
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectTree\Tree\TreeCtrl: ilObjSrContainerObjectTreeGUI
 * @ilCtrl_isCalledBy srag\Plugins\SrContainerObjectTree\UserSettings\UserSettingsCtrl: ilObjSrContainerObjectTreeGUI
 */
class ilObjSrContainerObjectTreeGUI extends ilObjectPluginGUI
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const CMD_PERMISSIONS = "perm";
    const CMD_SETTINGS = "settings";
    const CMD_SETTINGS_STORE = "settingsStore";
    const CMD_SHOW_CONTENTS = "showContents";
    const LANG_MODULE_OBJECT = "object";
    const LANG_MODULE_SETTINGS = "settings";
    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    const TAB_CONTENTS = "contents";
    const TAB_PERMISSIONS = "perm_settings";
    const TAB_SETTINGS = "settings";
    const TAB_SHOW_CONTENTS = "show_contents";
    /**
     * @var ilObjSrContainerObjectTree
     */
    public $object;


    /**
     * @return string
     */
    public static function getStartCmd() : string
    {
        return self::CMD_SHOW_CONTENTS;
    }


    /**
     * @inheritDoc
     *
     * @param ilObjSrContainerObjectTree $a_new_object
     */
    public function afterSave(/*ilObjSrContainerObjectTree*/ ilObject $a_new_object)/* : void*/
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
        return ilSrContainerObjectTreePlugin::PLUGIN_ID;
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
        self::dic()->help()->setScreenIdComponent(ilSrContainerObjectTreePlugin::PLUGIN_ID);
        self::dic()->ui()->mainTemplate()->setPermanentLink(ilSrContainerObjectTreePlugin::PLUGIN_ID, $this->object->getRefId());

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(FormBuilder::class):
                self::dic()->ctrl()->forwardCommand(self::srContainerObjectTree()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object));
                break;

            case strtolower(TreeCtrl::class):
                self::dic()->tabs()->activateTab(self::TAB_SHOW_CONTENTS);
                self::dic()->ctrl()->forwardCommand(new TreeCtrl(
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_ALLOWED_EMPTY_CONTAINER_OBJECT_TYPES),
                    $this->object->getContainerRefId(),
                    self::dic()->ctrl()->getLinkTargetByClass(UserSettingsCtrl::class, UserSettingsCtrl::CMD_EDIT_USER_SETTINGS, "", true),
                    self::plugin()->translate("error", UserSettingsCtrl::LANG_MODULE),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_LINK_CONTAINER_OBJECTS),
                    $this->getUserSettings()->getMaxDeep(
                        self::srContainerObjectTree()->tree()->getTreeEndDeep(
                            $this->object->getContainerRefId()
                        )
                    ),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_MAX_DEEP_METHOD),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_MAX_DEEP_METHOD_START_HIDE_METADATA),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_MAX_DEEP_METHOD_START_SHOW_ARROW),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_OBJECT_TYPES),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_ONLY_SHOW_CONTAINER_OBJECTS_IF_NOT_EMPTY),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_OPEN_LINKS_IN_NEW_TAB),
                    self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_RECURSIVE_COUNT),
                    $this->getUserSettings()->isShowDescription()
                ));
                break;

            case strtolower(UserSettingsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new UserSettingsCtrl(
                    $this->getUserSettings(),
                    self::srContainerObjectTree()->tree()->getTreeStartDeep(),
                    self::srContainerObjectTree()->tree()->getTreeEndDeep(
                        $this->object->getContainerRefId()
                    )
                ));
                break;

            default:
                switch ($cmd) {
                    case self::CMD_SHOW_CONTENTS:
                        // Read commands
                        if (!ilObjSrContainerObjectTreeAccess::hasReadAccess()) {
                            ilObjSrContainerObjectTreeAccess::redirectNonAccess(ilRepositoryGUI::class);
                        }

                        $this->{$cmd}();
                        break;

                    case self::CMD_SETTINGS:
                    case self::CMD_SETTINGS_STORE:
                        // Write commands
                        if (!ilObjSrContainerObjectTreeAccess::hasWriteAccess()) {
                            ilObjSrContainerObjectTreeAccess::redirectNonAccess($this);
                        }

                        $this->{$cmd}();
                        break;

                    default:
                        // Unknown command
                        ilObjSrContainerObjectTreeAccess::redirectNonAccess(ilRepositoryGUI::class);
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
     * @return UserSettings
     */
    protected function getUserSettings() : UserSettings
    {
        return self::srContainerObjectTree()->userSettings()->getUserSettings(self::dic()->user()->getId(),
            (self::srContainerObjectTree()->config()->getValue(ConfigFormBuilder::KEY_USER_SETTINGS_PER_OBJECT) ? $this->obj_id : null));
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
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

            self::dic()->tabs()->addTab(self::TAB_SHOW_CONTENTS, self::plugin()->translate("show_contents", self::LANG_MODULE_OBJECT), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

            if (ilObjSrContainerObjectTreeAccess::hasWriteAccess()) {
                self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings", self::LANG_MODULE_SETTINGS), self::dic()->ctrl()
                    ->getLinkTarget($this, self::CMD_SETTINGS));
            }

            if (ilObjSrContainerObjectTreeAccess::hasEditPermissionAccess()) {
                self::dic()->tabs()->addTab(self::TAB_PERMISSIONS, self::plugin()->translate(self::TAB_PERMISSIONS, "", [], false), self::dic()->ctrl()
                    ->getLinkTargetByClass([
                        self::class,
                        ilPermissionGUI::class
                    ], self::CMD_PERMISSIONS));
            }

            self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
        }
    }


    /**
     *
     */
    protected function settings()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srContainerObjectTree()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function settingsStore()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srContainerObjectTree()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE_SETTINGS), true);

        self::dic()->ctrl()->redirect($this, self::CMD_SETTINGS);
    }


    /**
     *
     */
    protected function showContents()/* : void*/
    {
        self::dic()->ctrl()->redirectByClass(TreeCtrl::class);
    }
}
