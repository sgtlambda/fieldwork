<?php

namespace fieldwork\validators;

class RegexFieldValidator extends FieldValidator
{

    private $pattern,
        $localPattern;

    public function __construct ($pattern, $errorMsg, $localPattern = null)
    {
        parent::__construct($errorMsg);
        $this->pattern      = $pattern;
        $this->localPattern = $localPattern !== null ? $localPattern : $pattern;
    }

    public function getJsonData ()
    {
        return array_merge(parent::getJsonData(), array(
            'pattern' => $this->localPattern
        ));
    }

    public function isValid ($value)
    {
        return preg_match($this->pattern, $value);
    }

    public function describeObject ()
    {
        return 'regex';
    }

}