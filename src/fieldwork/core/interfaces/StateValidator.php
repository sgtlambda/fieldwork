<?php

namespace fieldwork\core\interfaces;

use fieldwork\core\State;
use fieldwork\core\reflection\StateValidationReflection;

interface StateValidator
{

    /**
     * Validates the provided state
     *
     * @param State $state
     *
     * @return StateValidationReflection
     */
    public function validate (State $state);
}