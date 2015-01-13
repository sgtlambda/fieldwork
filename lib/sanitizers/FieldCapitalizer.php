<?php

namespace jannieforms\sanitizers;

use jannieforms\sanitizers;

class FieldCapitalizer extends sanitizers\FieldSanitizer
{

    public function sanitize ($value)
    {
        return ucwords($value);
    }

    public function isLive ()
    {
        return true;
    }

    public function describeObject ()
    {
        return 'capitalize';
    }

}