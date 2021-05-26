<?php

namespace srag\CustomInputGUIs\SrContainerObjectTree;

/**
 * Trait CustomInputGUIsTrait
 *
 * @package srag\CustomInputGUIs\SrContainerObjectTree
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
