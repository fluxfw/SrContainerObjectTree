<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use srag\CustomInputGUIs\SrCurriculum\Loader\CustomInputGUIsLoaderDetector;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;
use srag\RemovePluginDataConfirm\SrCurriculum\RepositoryObjectPluginUninstallTrait;

/**
 * Class ilSrCurriculumPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrCurriculumPlugin extends ilRepositoryObjectPlugin
{

    use RepositoryObjectPluginUninstallTrait;
    use SrCurriculumTrait;

    const PLUGIN_CLASS_NAME = self::class;
    const PLUGIN_ID = "xsrc";
    const PLUGIN_NAME = "SrCurriculum";
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * ilSrCurriculumPlugin constructor
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
    }


    /**
     * @inheritDoc
     */
    protected function deleteData()/* : void*/
    {
        self::srCurriculum()->dropTables();
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return true;
    }
}
