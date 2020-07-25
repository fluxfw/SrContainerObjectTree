<?php

namespace srag\CustomInputGUIs\SrCurriculum;

/**
 * Trait CustomInputGUIsTrait
 *
 * @package srag\CustomInputGUIs\SrCurriculum
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait CustomInputGUIsTrait
{

    /**
     * @return CustomInputGUIs
     */
    protected static final function customInputGUIs() : CustomInputGUIs
    {
        return CustomInputGUIs::getInstance();
    }
}
