<?php

namespace fieldwork\validators;

class FieldLengthValidator extends RegexFieldValidator
{

    public function __construct ($min, $max, $allowed = null, $message = null)
    {
        if ($allowed == null)
            $allowed = 'a-zéëèA-Z0-9 .\-\'\"';
        if ($message == null)
            $message = "Moet minimaal $min en maximaal $max tekens bevatten.";
        parent::__construct("/^[" . $allowed . "]{" . $min . "," . $max . "}$/", $message);
    }

}