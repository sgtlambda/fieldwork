<?php

namespace jannieforms\sanitizers;

use jannieforms\sanitizers;

class FieldUppercaser extends sanitizers\AbstractFieldSaniziter
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