<?php

namespace fieldwork\tests\validators;

use fieldwork\validators\FieldValidator;

abstract class FieldValidator_TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FieldValidator The validator to test
     */
    private $validator;

    function __construct (FieldValidator $validator = null)
    {
        $this->validator = $validator;
    }

    /**
     * @param FieldValidator $validator
     */
    public function setValidator ($validator)
    {
        $this->validator = $validator;
    }

    /**
     * Asserts that a value passes validation with the currently set validator
     *
     * @param $value
     */
    function assertValid ($value)
    {
        self::assertThat($value, self::isValid($this->validator), '');
    }

    /**
     * Asserts that a value does not pass validation with the currently set validator
     * @param $value
     */
    function assertInvalid ($value)
    {
        self::assertThat($value, self::isInvalid($this->validator), '');
    }

    /**
     * Returns a FieldValidator_Constraint_IsValid matcher object
     *
     * @param FieldValidator $fieldValidator
     *
     * @return FieldValidator_Constraint_IsValid
     */
    public static function isValid (FieldValidator $fieldValidator)
    {
        return new FieldValidator_Constraint_IsValid($fieldValidator);
    }

    /**
     * Returns a FieldValidator_Constraint_IsInvalid matcher object
     *
     * @param FieldValidator $fieldValidator
     *
     * @return FieldValidator_Constraint_IsInvalid
     */
    public static function isInvalid (FieldValidator $fieldValidator)
    {
        return new FieldValidator_Constraint_IsInvalid($fieldValidator);
    }
}