<?php

namespace fieldwork\sanitizers;

class RegexReplaceSanitizer extends FieldSanitizer
{

    protected $from, $to;

    function __construct ($from, $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }


    public function sanitize ($value)
    {
        return preg_replace($this->from, $this->to, $value);
    }

    public function isLive ()
    {
        return false;
    }

    public function describeObject ()
    {
        return 'regex';
    }

}