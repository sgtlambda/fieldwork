<?php

namespace fieldwork\validators;

class FieldContentValidator extends RegexFieldValidator
{

    public function __construct ($error = "Input required")
    {
        parent::__construct("/./", $error);
    }

}