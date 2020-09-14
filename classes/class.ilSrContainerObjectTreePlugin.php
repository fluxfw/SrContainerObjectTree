<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use srag\CustomInputGUIs\SrContainerObjectTree\Loader\CustomInputGUIsLoaderDetector;
use srag\DIC\SrContainerObjectTree\DevTools\DevToolsCtrl;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;
use srag\RemovePluginDataConfirm\SrContainerObjectTree\RepositoryObjectPluginUninstallTrait;

/**
 * Class ilSrContainerObjectTreePlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrContainerObjectTreePlugin extends ilRepositoryObjectPlugin
{

    use RepositoryObjectPluginUninstallTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = self::class;
    const PLUGIN_ID = "xcot";
    const PLUGIN_NAME = "SrContainerObjectTree";
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * ilSrContainerObjectTreePlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    public function allowCopy() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }


    /**
     * @inheritDoc
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null)/* : void*/
    {
        parent::updateLanguages($a_lang_keys);

        $this->installRemovePluginDataConfirmLanguages();

        DevToolsCtrl::installLanguages(self::plugin());
    }


    /**
     * @inheritDoc
     */
    protected function deleteData()/* : void*/
    {
        self::srContainerObjectTree()->dropTables();
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return true;
    }
}