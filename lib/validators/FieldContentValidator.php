<?php

namespace jannieforms\validators;

use jannieforms\validators\RegexFieldValidator;

class FieldContentValidator extends RegexFieldValidator
{

    public function __construct ($error = "Verplicht veld.")
    {
        parent::__construct("/./", $error);
    }

}