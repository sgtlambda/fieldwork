<?php

namespace fieldwork\validators;

use Egulias\EmailValidator\EmailValidator;

class EmailFieldValidator extends RegexFieldValidator
{

    const PATT  = "/^.*@.*\\.[A-Z]{2,}$/i"; // This is a very simple version of the email validation regular expression which is only used client-side.
    const ERROR = "Enter a valid email address";

    private $validator;

    public function __construct ()
    {
        parent::__construct(self::PATT, self::ERROR);
        $this->validator = new EmailValidator();
    }

    public function isValid ($value)
    {
        return $this->validator->isValid($value);
    }

}