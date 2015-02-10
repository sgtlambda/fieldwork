<?php

namespace fieldwork\validators;



class PhoneFieldValidator extends RegexFieldValidator
{

    const PATT  = "/^[0-9.+()\\/ -]{4,}$/";
    const ERROR = "Vul een geldig telefoonnummer in.";

    public function __construct ()
    {
        parent::__construct(self::PATT, self::ERROR);
    }

}