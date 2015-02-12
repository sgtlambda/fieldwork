<?php

namespace fieldwork\tests\validators;

use fieldwork\validators\EmailFieldValidator;

class EmailFieldValidatorTest extends FieldValidator_TestCase
{

    function __construct ()
    {
        $validator = new EmailFieldValidator();
        parent::__construct($validator);
    }

    public function testValidation ()
    {
        $this->assertValid('user@longtold.website');
        $this->assertValid('user.name-dash+symbol@example.org');
        $this->assertValid('üñîçøðé@üñîçøðé.com');
        $this->assertValid('!#$%&\'*+-/=?^_`{}|~@example.org');
        $this->assertInvalid('');
        $this->assertInvalid('Abc.example.com');
        $this->assertInvalid('john..doe@example.com');
        $this->assertInvalid('john.doe@example..com');

    }

}