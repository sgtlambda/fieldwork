<?php

namespace fieldwork\validators;

class FieldLengthValidator extends RegexFieldValidator
{

    /**
     * @param int    $min          The minimum number of allowed characters
     * @param int    $max          The maximum number of allowed characters
     * @param string $allowed      The allowed characters as a regex sequence
     * @param string $error        The string to display if the field does not pass validation
     * @param bool   $requireInput If false, the user can also leave the field empty
     */
    public function __construct ($min, $max, $allowed = ".", $error = "Input must consist of at least %d and no more than %d characters", $requireInput = true)
    {
        parent::__construct("/^(" . $allowed . "{" . $min . "," . $max . "})" . ($requireInput ? "" : "?") . "$/", sprintf($error, $min, $max));
    }

}