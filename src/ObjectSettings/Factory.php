<?php

namespace srag\Plugins\SrCurriculum\ObjectSettings;

use ilObjSrCurriculum;
use ilObjSrCurriculumGUI;
use ilSrCurriculumPlugin;
use srag\DIC\SrCurriculum\DICTrait;
use srag\Plugins\SrCurriculum\ObjectSettings\Form\FormBuilder;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrCurriculum\ObjectSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrCurriculumTrait;

    const PLUGIN_CLASS_NAME = ilSrCurriculumPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

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
     * @param ilObjSrCurriculumGUI $parent
     * @param ilObjSrCurriculum    $object
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(ilObjSrCurriculumGUI $parent, ilObjSrCurriculum $object) : FormBuilder
    {
        $form = new FormBuilder($parent, $object);

        return $form;
    }


    /**
     * @return ObjectSettings
     */
    public function newInstance() : ObjectSettings
    {
        $object_settings = new ObjectSettings();

        return $object_settings;
    }
}
