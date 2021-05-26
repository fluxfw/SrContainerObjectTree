<?php

namespace srag\CustomInputGUIs\SrContainerObjectTree\InputGUIWrapperUIInputComponent;

use ILIAS\Refinery\Constraint;
use ILIAS\Refinery\Custom\Constraint as CustomConstraint;

/**
 * Class InputGUIWrapperConstraint
 *
 * @package srag\CustomInputGUIs\SrContainerObjectTree\InputGUIWrapperUIInputComponent
 */
class InputGUIWrapperConstraint extends CustomConstraint implements Constraint
{

    use InputGUIWrapperConstraintTrait;
}
