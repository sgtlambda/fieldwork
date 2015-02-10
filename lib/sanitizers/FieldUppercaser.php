<?php

namespace fieldwork\sanitizers;

use fieldwork\sanitizers;

class FieldUppercaser extends sanitizers\FieldSanitizer
{

    public function sanitize ($value)
    {
        return strtoupper($value);
    }

    public function isLive ()
    {
        return true;
    }

    public function describeObject ()
    {
        return 'uppercase';
    }

}