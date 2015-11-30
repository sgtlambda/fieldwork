<?php

namespace fieldwork\core\reflection;

use fieldwork\core\reflection\ValidationError;

class StateValidationReflection
{

    /**
     * @var ValidationError[]
     */
    private $errors;

    /**
     * StateReflection constructor.
     * @param ValidationError[] $errors
     */
    public function __construct (array $errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * @return ValidationError[]
     */
    public function getErrors ()
    {
        return $this->errors;
    }
}