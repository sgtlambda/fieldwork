<?php

namespace fieldwork\core;

use fieldwork\core\interfaces\Sanitizer;
use fieldwork\core\interfaces\Symmetrical;

abstract class SymmetricalSanitizer implements Symmetrical, Sanitizer
{

    public function serialize ()
    {
        return array(
            'method' => $this->describeObject()
        );
    }
}