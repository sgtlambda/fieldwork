<?php

namespace fieldwork\core\interfaces;

use fieldwork\core\State;

interface Sanitizer
{

    /**
     * Sanitizes the provided state
     *
     * @param State $state
     */
    public function sanitize (State $state);
}