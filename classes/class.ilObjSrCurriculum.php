<?php

use srag\DIC\SrCurriculum\DICTrait;
use srag\Plugins\SrCurriculum\ObjectSettings\ObjectSettings;
use srag\Plugins\SrCurriculum\Utils\SrCurriculumTrait;

/**
 * Class ilObjSrCurriculum
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilObjSrCurriculum extends ilObjectPlugin
{

    use DICTrait;
    use SrCurriculumTrait;

    const PLUGIN_CLASS_NAME = ilSrCurriculumPlugin::class;
    /**
     * @var ObjectSettings
     */
    protected $object_settings;


    /**
     * ilObjSrCurriculum constructor
     *
     * @param int $a_ref_id
     */
    public function __construct(/*int*/ $a_ref_id = 0)
    {
        parent::__construct($a_ref_id);
    }


    /**
     * @inheritDoc
     */
    public function doCreate()/* : void*/
    {
        $this->object_settings = self::srCurriculum()->objectSettings()->factory()->newInstance();

        $this->object_settings->setObjId($this->id);

        self::srCurriculum()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @inheritDoc
     */
    public function doDelete()/* : void*/
    {
        if ($this->object_settings !== null) {
            self::srCurriculum()->objectSettings()->deleteObjectSettings($this->object_settings);
        }
    }


    /**
     * @inheritDoc
     */
    public function doRead()/* : void*/
    {
        $this->object_settings = self::srCurriculum()->objectSettings()->getObjectSettingsById(intval($this->id));
    }


    /**
     * @inheritDoc
     */
    public function doUpdate()/* : void*/
    {
        self::srCurriculum()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @inheritDoc
     */
    public final function initType()/* : void*/
    {
        $this->setType(ilSrCurriculumPlugin::PLUGIN_ID);
    }


    /**
     * @return bool
     */
    public function isOnline() : bool
    {
        return $this->object_settings->isOnline();
    }


    /**
     * @param bool $is_online
     */
    public function setOnline(bool $is_online = true)/* : void*/
    {
        $this->object_settings->setOnline($is_online);
    }


    /**
     * @inheritDoc
     *
     * @param ilObjSrCurriculum $new_obj
     */
    protected function doCloneObject(/*ilObjSrCurriculum*/ $new_obj, /*int*/ $a_target_id, /*?int*/ $a_copy_id = null)/* : void*/
    {
        $new_obj->object_settings = self::srCurriculum()->objectSettings()->cloneObjectSettings($this->object_settings);

        $new_obj->object_settings->setObjId($new_obj->id);

        self::srCurriculum()->objectSettings()->storeObjectSettings($new_obj->object_settings);
    }
}
