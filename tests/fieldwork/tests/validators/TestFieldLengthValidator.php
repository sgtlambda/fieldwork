<?php

namespace fieldwork\tests\validators;

use fieldwork\validators\FieldLengthValidator;

class FieldLengthValidatorTestCase extends FieldValidator_TestCase
{
    function __construct ()
    {
        $validator = new FieldLengthValidator(4, 8);
        parent::__construct($validator);
    }

    public function testValidation ()
    {
        $this->assertValid('1234');
        $this->assertValid('12345678');
        $this->assertInvalid('');
        $this->assertInvalid('123');
        $this->assertInvalid('123456789');
    }

}