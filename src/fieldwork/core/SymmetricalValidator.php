<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\Validator;

abstract class SymmetricalValidator implements Symmetrical, Validator
{

    public function serialize ()
    {
        return array(
            'method' => $this->describeObject()
        );
    }
}