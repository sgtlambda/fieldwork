<?php

namespace fieldwork\tests\validators;

use fieldwork\validators\FieldValidator;

abstract class FieldValidator_TestCase extends \PHPUnit_Framework_TestCase
{

    private $validator;

    function __construct (FieldValidator $validator)
    {
        $this->validator = $validator;
    }

    function assertValid ($value)
    {
        /* @var $this ->validator FieldValidator */
        $this->assertTrue($this->validator->isValid($value));
    }

    function assertInvalid ($value)
    {
        $this->assertFalse($this->validator->isValid($value));
    }

}