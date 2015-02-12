<?php

namespace fieldwork\tests\validators;

use fieldwork\validators\FieldValidator;

class FieldValidator_Constraint_IsValid extends \PHPUnit_Framework_Constraint
{

    private $validator;

    public function __construct (FieldValidator $validator)
    {
        parent::__construct();
        $this->validator = $validator;
    }

    protected function matches ($other)
    {
        return $this->validator->isValid($other);
    }

    public function toString ()
    {
        return "is valid";
    }

}