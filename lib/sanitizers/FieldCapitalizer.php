<?php

namespace jannieforms\sanitizers;

use jannieforms\sanitizers;

class FieldCapitalizer extends sanitizers\AbstractFieldSaniziter
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