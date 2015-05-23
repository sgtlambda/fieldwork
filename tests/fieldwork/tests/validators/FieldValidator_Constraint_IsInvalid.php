<?php

namespace fieldwork\tests\validators;

use fieldwork\validators\FieldValidator;

class FieldValidator_Constraint_IsInvalid extends \PHPUnit_Framework_Constraint
{

    private $fieldValidator;

    public function __construct (FieldValidator $fieldValidator)
    {
        parent::__construct();
        $this->fieldValidator = $fieldValidator;
    }

    protected function matches ($other)
    {
        return !$this->fieldValidator->isValid($other);
    }

    public function toString ()
    {
        return "is invalid";
    }

}