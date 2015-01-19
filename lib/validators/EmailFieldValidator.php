<?php

namespace jannieforms\validators;



class EmailFieldValidator extends RegexFieldValidator
{

    const PATT  = "/^[A-Z0-9._%\\-+]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i";
    const ERROR = "Vul een geldig e-mailadres in.";

    public function __construct ()
    {
        parent::__construct(self::PATT, self::ERROR);
    }

}