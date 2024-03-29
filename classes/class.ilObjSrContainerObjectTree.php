<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrContainerObjectTree\DICTrait;
use srag\Plugins\SrContainerObjectTree\ObjectSettings\ObjectSettings;
use srag\Plugins\SrContainerObjectTree\UserSettings\UserSettings;
use srag\Plugins\SrContainerObjectTree\Utils\SrContainerObjectTreeTrait;

/**
 * Class ilObjSrContainerObjectTree
 */
class ilObjSrContainerObjectTree extends ilObjectPlugin
{

    use DICTrait;
    use SrContainerObjectTreeTrait;

    const PLUGIN_CLASS_NAME = ilSrContainerObjectTreePlugin::class;
    /**
     * @var ObjectSettings|null
     */
    protected $object_settings = null;


    /**
     * ilObjSrContainerObjectTree constructor
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
    public function doCreate() : void
    {
        $this->object_settings = self::srContainerObjectTree()->objectSettings()->factory()->newInstance();

        $this->object_settings->setObjId($this->id);

        self::srContainerObjectTree()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @inheritDoc
     */
    public function doDelete() : void
    {
        if ($this->object_settings !== null) {
            self::srContainerObjectTree()->objectSettings()->deleteObjectSettings($this->object_settings);
        }
    }


    /**
     * @inheritDoc
     */
    public function doRead() : void
    {
        $this->object_settings = self::srContainerObjectTree()->objectSettings()->getObjectSettingsById(intval($this->id));
    }


    /**
     * @inheritDoc
     */
    public function doUpdate() : void
    {
        self::srContainerObjectTree()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @return int
     */
    public function getContainerRefId() : int
    {
        return $this->object_settings->getContainerRefId();
    }


    /**
     * @return int
     */
    public function getStartDeep() : int
    {
        return $this->getUserSettings()->getStartDeep();
    }


    /**
     * @return UserSettings
     */
    public function getUserSettings() : UserSettings
    {
        return self::srContainerObjectTree()->userSettings()->getUserSettingsByUserIdAndObjId(self::dic()->user()->getId(), $this->id);
    }


    /**
     * @inheritDoc
     */
    public final function initType() : void
    {
        $this->setType(ilSrContainerObjectTreePlugin::PLUGIN_ID);
    }


    /**
     * @return bool
     */
    public function isOnline() : bool
    {
        return $this->object_settings->isOnline();
    }


    /**
     * @return bool
     */
    public function isShowMetadata() : bool
    {
        return $this->getUserSettings()->isShowMetadata();
    }


    /**
     * @param int $container_ref_id
     */
    public function setContainerRefId(int $container_ref_id) : void
    {
        $this->object_settings->setContainerRefId($container_ref_id);
    }


    /**
     * @param bool $is_online
     */
    public function setOnline(bool $is_online = true) : void
    {
        $this->object_settings->setOnline($is_online);
    }


    /**
     * @param bool $show_metadata
     */
    public function setShowMetadata(bool $show_metadata) : void
    {
        $this->getUserSettings()->setShowMetadata($show_metadata);
    }


    /**
     * @param int $max_deep
     */
    public function setStartDeep(int $max_deep) : void
    {
        $this->getUserSettings()->setStartDeep($max_deep);
    }


    /**
     * @inheritDoc
     *
     * @param ilObjSrContainerObjectTree $new_obj
     */
    protected function doCloneObject(/*ilObjSrContainerObjectTree*/ $new_obj, /*int*/ $a_target_id, /*?int*/ $a_copy_id = null) : void
    {
        $new_obj->object_settings = self::srContainerObjectTree()->objectSettings()->cloneObjectSettings($this->object_settings);

        $new_obj->object_settings->setObjId($new_obj->id);

        self::srContainerObjectTree()->objectSettings()->storeObjectSettings($new_obj->object_settings);
    }
}
