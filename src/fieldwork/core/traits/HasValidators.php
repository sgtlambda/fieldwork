<?php

namespace fieldwork\core\traits;

use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\Validator;
use fieldwork\core\reflection\CompoundStateValidationReflection;
use fieldwork\core\State;
use fieldwork\core\reflection\StateValidationReflection;

trait HasValidators
{

    /**
     * @var Validator[]
     */
    private $validators;

    public function serialize ()
    {
        return [
            'validators' => array_filter($this->validators, function (Validator $validator) {
                return $validator instanceof Symmetrical;
            })
        ];
    }

    /**
     * Adds a validator to the validation stack.
     * @param Validator $validator
     * @return $this
     */
    public function addValidator (Validator $validator)
    {
        array_push($this->validators, $validator);
        return $this;
    }

    /**
     * Validates the provided state
     * @param State $state
     * @return StateValidationReflection
     */
    public function validate (State $state)
    {
        $reflections = [];
        foreach ($this->validators as $validator)
            array_push($reflections, $validator->validate($state));
        return new CompoundStateValidationReflection($reflections);
    }
}