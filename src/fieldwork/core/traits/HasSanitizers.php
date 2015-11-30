<?php

namespace fieldwork\core\traits;

use fieldwork\core\interfaces\Sanitizer;
use fieldwork\core\interfaces\Symmetrical;
use fieldwork\core\interfaces\Validator;
use fieldwork\core\State;

trait HasSanitizers
{

    /**
     * @var Sanitizer[]
     */
    private $sanitizers;

    public function serialize ()
    {
        return [
            'sanitizers' => array_filter($this->sanitizers, function (Validator $validator) {
                return $validator instanceof Symmetrical;
            })
        ];
    }

    /**
     * Adds a sanitizer to the sanitizer stack.
     * @param Sanitizer $sanitizer
     * @return $this
     */
    public function addSanitizer (Sanitizer $sanitizer)
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