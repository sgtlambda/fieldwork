<?php

namespace fieldwork\core\traits;

use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\StateValidator;
use fieldwork\core\reflection\CompoundStateValidationReflection;
use fieldwork\core\State;
use fieldwork\core\reflection\StateValidationReflection;

trait SimpleImplementedValidator
{

    /**
     * @var StateValidator[]
     */
    private $validators;

    public function serialize ()
    {
        return ['validators' => array_filter($this->validators, function (StateValidator $validator) {
            return $validator instanceof Symmetrical;
        })];
    }

    /**
     * Adds a validator to the validation stack.
     * @param StateValidator $validator
     * @return $this
     */
    public function addValidator (StateValidator $validator)
    {
        array_push($this->validators, $validator);
        return $this;
    }

    /**
     * Validates the provided state
     * @param State $state
     * @return CompoundStateValidationReflection
     */
    public function validate (State $state)
    {
        $reflections = [];
        foreach ($this->validators as $validator)
            array_push($reflections, $validator->validate($state));
        return new CompoundStateValidationReflection($reflections);
    }
}