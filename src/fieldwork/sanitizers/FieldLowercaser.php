<?php

namespace fieldwork\sanitizers;

use fieldwork\sanitizers;

class FieldLowercaser extends sanitizers\FieldSanitizer
{

    public function sanitize ($value)
    {
        return strtolower($value);
    }

    public function isLive ()
    {
        return true;
    }

    public function describeObject ()
    {
        return 'lowercase';
    }

}