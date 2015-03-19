<?php

namespace fieldwork\validators;



class PhoneFieldValidator extends RegexFieldValidator
{

    const PATT  = "/^[0-9.+()\\/ -]{4,}$/";
    const ERROR = "Not a valid phone number";

    public function __construct ($error = self::ERROR)
    {
        parent::__construct(self::PATT, $error);
    }

}