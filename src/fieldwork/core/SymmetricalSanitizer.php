<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\StateSanitizer;
use fieldwork\core\interfaces\Symmetrical;

abstract class SymmetricalStateSanitizer implements Symmetrical, StateSanitizer
{

    public function serialize ()
    {
        return array(
            'method' => $this->describeObject()
        );
    }
}