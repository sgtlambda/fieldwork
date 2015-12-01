<?php

namespace fieldwork\core\traits;

use fieldwork\core\Component;
use fieldwork\core\interfaces\StateSanitizer;
use fieldwork\core\State;

trait CompoundImplementedSanitizer
{

    use SimpleImplementedSanitizer, CompoundOperator {
        sanitize as simpleSanitize;
    }

    public function sanitize (State $state)
    {
        $this->doCompoundOperation($state, function (Component $component) {
            return $component instanceof StateSanitizer;
        }, function (StateSanitizer $component, State $state) {
            $component->sanitize($state);
        });
        $this->simpleSanitize($state);
    }
}