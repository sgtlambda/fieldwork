<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\StateValidator;

abstract class SymmetricalStateValidator implements Symmetrical, StateValidator
{

    public function serialize ()
    {
        return array(
            'method' => $this->describeObject()
        );
    }
}