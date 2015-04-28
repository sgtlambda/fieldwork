<?php

namespace fieldwork\validators;

use IBAN\Validation\IBANValidator;

class IbanFieldValidator extends RegexFieldValidator
{

    const PATT       = "/^[A-Z]{2}[0-9]{2} [A-Z0-9]{4} [0-9]{4} [0-9]{4}( [0-9]{4})?( [0-9]{2})?$/";
    const PATT_LOCAL = "/^([A-Z]{2}[0-9]{2} [A-Z0-9]{4} [0-9]{4} [0-9]{4}( [0-9]{4})?( [0-9]{2})?|[0-9]{1,10})$/";
    const ERROR      = "Not a valid IBAN";

    private $validator;

    public function __construct ()
    {
        parent::__construct(
            self::PATT, self::ERROR, self::PATT_LOCAL
        );
        $this->validator = new IBANValidator();
    }

    public function isValid ($value)
    {
        $sanitized = preg_replace('/\s/', '', $value);
        return $this->validator->validate($sanitized);
    }
}
