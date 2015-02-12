<?php

namespace fieldwork\validators;

class FieldLengthValidator extends RegexFieldValidator
{

    public function __construct ($min, $max, $allowed = ".", $error = "Input must consist of at least %d and no more than %d characters")
    {
        parent::__construct("/^" . $allowed . "{" . $min . "," . $max . "}$/", sprintf($error, $min, $max));
    }

}