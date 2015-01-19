<?php

namespace jannieforms\validators;

use jannieforms\validators;

/**
 * Adding this validator to the front of a field's validation stack will cause the rest of the validators to be skipped
 * (and effectively evaluated to true) if the value of the field equals $breakValue
 * @package jannieforms\validators
 */
class ExactValueContinuationValidator extends validators\FieldValidator
{

    private $breakValue = "";

    function __construct ($breakValue)
    {
        $this->breakValue = $breakValue;
    }

    function describeObject ()
    {
        return 'breakonvalue';
    }

    public function isValid ($value)
    {
        return true;
    }

    public function skipValidation ($value)
    {
        return $value === $this->breakValue;
    }


}