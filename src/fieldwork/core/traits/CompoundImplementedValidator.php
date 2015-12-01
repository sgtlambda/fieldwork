<?php

namespace fieldwork\core\traits;

use fieldwork\core\Component;
use fieldwork\core\interfaces\StateValidator;
use fieldwork\core\reflection\CompoundStateValidationReflection;
use fieldwork\core\State;

trait CompoundImplementedValidator
{

    use SimpleImplementedValidator, CompoundOperator {
        validate as simpleValidate;
    }

    public function validate (State $state)
    {
        $reflections = [];
        $this->doCompoundOperation($state, function (Component $component) {
            return $component instanceof StateValidator;
        }, function (StateValidator $component, State $state) use (&$reflections) {
            $reflections = array_merge($reflections, $component->validate($state));
        });
        return new CompoundStateValidationReflection(array_merge($reflections, $this->simpleValidate($state)->getReflections()));
    }
}