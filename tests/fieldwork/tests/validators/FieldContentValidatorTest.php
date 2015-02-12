<?php

namespace fieldwork\tests\validators;

use fieldwork\validators\FieldContentValidator;

class FieldContentValidatorTest extends FieldValidator_TestCase
{

    function __construct ()
    {
        $validator = new FieldContentValidator();
        parent::__construct($validator);
    }

    public function testValidation ()
    {
        $this->assertValid('foo');
        $this->assertInvalid('');
    }

}