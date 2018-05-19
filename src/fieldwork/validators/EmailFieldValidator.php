<?php

namespace fieldwork\validators;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

class EmailFieldValidator extends RegexFieldValidator
{

    const PATT  = "/^.*@.*\\.[A-Z]{2,}$/i"; // This is a very simple version of the email validation regular expression which is only used client-side.

    private $validator;

    public function __construct ($error = "Enter a valid email address")
    {
        parent::__construct(self::PATT, $error);
        $this->validator = new EmailValidator();
    }

    public function isValid ($value)
    {
        return $this->validator->isValid($value, new RFCValidation());
    }
}
