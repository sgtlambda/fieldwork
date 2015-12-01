<?php

namespace fieldwork\core\traits;

use fieldwork\core\interfaces\StateSanitizer;
use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\StateValidator;
use fieldwork\core\State;

trait SimpleImplementedSanitizer
{

    /**
     * @var StateSanitizer[]
     */
    private $sanitizers;

    public function serialize ()
    {
        return ['sanitizers' => array_filter($this->sanitizers, function (StateValidator $validator) {
            return $validator instanceof Symmetrical;
        })];
    }

    /**
     * Adds a sanitizer to the sanitizer stack.
     * @param StateSanitizer $sanitizer
     * @return $this
     */
    public function addSanitizer (StateSanitizer $sanitizer)
    {
        array_push($this->sanitizers, $sanitizer);
        return $this;
    }

    /**
     * Sanitizes the provided state
     * @param State $state
     */
    public function sanitize (State $state)
    {
        foreach ($this->sanitizers as $sanitizer)
            $sanitizer->sanitize($state);
    }
}