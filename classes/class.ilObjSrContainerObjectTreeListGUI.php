<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class ilObjSrContainerObjectTreeListGUI
 */
class ilObjSrContainerObjectTreeListGUI extends ilObjectPluginListGUI
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;


    /**
     * ilObjSrContainerObjectTreeListGUI constructor
     *
     * @param int $a_context
     */
    public function __construct(/*int*/ $a_context = self::CONTEXT_REPOSITORY)
    {
        parent::__construct($a_context);
    }


    /**
     * @inheritDoc
     */
    public function getCommandFrame(/*string*/ $a_cmd) : string
    {
        switch ($a_cmd) {
            case ilObjSrContainerObjectTreeGUI::getStartCmd():
                if (self::srContainerObjectTree()->config()->isOpenLinksInNewTab()) {
                    return "_blank";
                } else {
                    return parent::getCommandFrame($a_cmd);
                }

            default:
                return parent::getCommandFrame($a_cmd);
        }
    }


    /**
     * @inheritDoc
     */
    public function getGuiClass() : string
    {
        return ilObjSrContainerObjectTreeGUI::class;
    }


    /**
     * @inheritDoc
     */
    public function getProperties() : array
    {
        $props = [];

        if (ilObjSrContainerObjectTreeAccess::_isOffline($this->obj_id)) {
            $props[] = [
                "alert"    => true,
                "property" => self::plugin()->translate("status", ilObjSrContainerObjectTreeGUI::LANG_MODULE_OBJECT),
                "value"    => self::plugin()->translate("offline", ilObjSrContainerObjectTreeGUI::LANG_MODULE_OBJECT)
            ];
        }

        return $props;
    }


    /**
     * @inheritDoc
     */
    public function initCommands() : array
    {
        $this->commands_enabled = true;
        $this->copy_enabled = true;
        $this->cut_enabled = true;
        $this->delete_enabled = true;
        $this->description_enabled = true;
        $this->notice_properties_enabled = true;
        $this->properties_enabled = true;
        $this->subscribe_enabled = true;

        $this->comments_enabled = false;
        $this->comments_settings_enabled = false;
        $this->expand_enabled = false;
        $this->info_screen_enabled = false;
        $this->link_enabled = false;
        $this->notes_enabled = false;
        $this->payment_enabled = false;
        $this->preconditions_enabled = false;
        $this->rating_enabled = false;
        $this->rating_categories_enabled = false;
        $this->repository_transfer_enabled = false;
        $this->search_fragment_enabled = false;
        $this->static_link_enabled = false;
        $this->tags_enabled = false;
        $this->timings_enabled = false;

        $commands = [
            [
                "permission" => "read",
                "cmd"        => ilObjSrContainerObjectTreeGUI::getStartCmd(),
                "default"    => true
            ]
        ];

        return $commands;
    }


    /**
     * @inheritDoc
     */
    public function initType() : void
    {
        $this->setType(ilSrContainerObjectTreePlugin::PLUGIN_ID);
    }
}
